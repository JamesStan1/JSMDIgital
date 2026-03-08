<?php
/**
 * JSM Digital — /api/dashboard/cards
 *
 * GET    — all cards for a board  (?board_id=N)
 * POST   — create a card
 * PUT    — update a card          (?id=N)
 * DELETE — delete a card          (?id=N)
 */

require_once __DIR__ . '/../../../helpers/functions.php';

setDashboardCorsHeaders();
$user = requireAuth();

$pdo    = getDbConnection();
$method = $_SERVER['REQUEST_METHOD'];

/* ── GET ──────────────────────────────────────────────────── */
if ($method === 'GET') {
    $boardId = getQueryInt('board_id');
    if (!$boardId) {
        jsonError('board_id is required.', 400);
    }

    $stmt = $pdo->prepare(
        "SELECT c.*,
                u.full_name AS created_by_name,
                GROUP_CONCAT(DISTINCT su.id         ORDER BY su.full_name SEPARATOR ',') AS assigned_ids,
                GROUP_CONCAT(DISTINCT su.full_name  ORDER BY su.full_name SEPARATOR ', ') AS assigned_names
           FROM board_cards c
           JOIN board_lists l  ON c.list_id    = l.id
           JOIN staff_users u  ON c.created_by = u.id
           LEFT JOIN card_assignments ca ON ca.card_id  = c.id
           LEFT JOIN staff_users su      ON su.id       = ca.staff_id
          WHERE l.board_id = :bid
          GROUP BY c.id
          ORDER BY c.list_id ASC, c.position ASC, c.id ASC"
    );
    $stmt->execute([':bid' => $boardId]);
    $cards = $stmt->fetchAll();

    foreach ($cards as &$c) {
        $c['id']          = (int) $c['id'];
        $c['list_id']     = (int) $c['list_id'];
        $c['position']    = (int) $c['position'];
        $c['created_by']  = (int) $c['created_by'];
        $c['assigned_ids'] = $c['assigned_ids']
            ? array_map('intval', explode(',', $c['assigned_ids']))
            : [];
    }

    jsonSuccess('OK', ['cards' => $cards]);
}

/* ── POST ─────────────────────────────────────────────────── */
if ($method === 'POST') {
    $body        = getRequestBody();
    $listId      = (int)   ($body['list_id']     ?? 0);
    $title       = cleanString($body['title']       ?? '', 500);
    $description = cleanString($body['description'] ?? '', 5000);
    $priority    = cleanString($body['priority']    ?? 'medium', 20);
    $dueDate     = cleanString($body['due_date']    ?? '', 10);

    $allowedPriorities = ['low', 'medium', 'high', 'urgent'];
    if (!in_array($priority, $allowedPriorities, true)) {
        $priority = 'medium';
    }
    if (!$listId || $title === '') {
        jsonError('list_id and title are required.', 422);
    }
    $dueDate = ($dueDate && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dueDate)) ? $dueDate : null;

    // Next position within this list
    $pos = $pdo->prepare(
        'SELECT COALESCE(MAX(position), -1) + 1 FROM board_cards WHERE list_id = :lid'
    );
    $pos->execute([':lid' => $listId]);
    $position = (int) $pos->fetchColumn();

    $stmt = $pdo->prepare(
        'INSERT INTO board_cards (list_id, title, description, priority, due_date, position, created_by)
         VALUES (:lid, :t, :d, :p, :dd, :pos, :u)'
    );
    $stmt->execute([
        ':lid' => $listId,
        ':t'   => $title,
        ':d'   => $description,
        ':p'   => $priority,
        ':dd'  => $dueDate,
        ':pos' => $position,
        ':u'   => $user['id'],
    ]);
    $cardId = (int) $pdo->lastInsertId();

    // Assign staff members if provided
    if (!empty($body['assigned_ids']) && is_array($body['assigned_ids'])) {
        $asgn = $pdo->prepare(
            'INSERT IGNORE INTO card_assignments (card_id, staff_id) VALUES (:cid, :sid)'
        );
        foreach ($body['assigned_ids'] as $sid) {
            $asgn->execute([':cid' => $cardId, ':sid' => (int) $sid]);
        }
    }

    jsonSuccess('Card created.', ['id' => $cardId]);
}

/* ── PUT ──────────────────────────────────────────────────── */
if ($method === 'PUT') {
    $id = getQueryInt('id');
    if (!$id) {
        jsonError('Card ID is required.', 400);
    }

    $body   = getRequestBody();
    $fields = [];
    $params = [':id' => $id];

    if (isset($body['title'])) {
        $fields[] = 'title = :title';
        $params[':title'] = cleanString($body['title'], 500);
    }
    if (isset($body['description'])) {
        $fields[] = 'description = :description';
        $params[':description'] = cleanString($body['description'], 5000);
    }
    if (isset($body['priority'])) {
        $p = cleanString($body['priority'], 20);
        if (in_array($p, ['low', 'medium', 'high', 'urgent'], true)) {
            $fields[] = 'priority = :priority';
            $params[':priority'] = $p;
        }
    }
    if (isset($body['due_date'])) {
        $dd = cleanString($body['due_date'], 10);
        $fields[] = 'due_date = :due_date';
        $params[':due_date'] = ($dd && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dd)) ? $dd : null;
    }
    if (isset($body['list_id'])) {
        $fields[] = 'list_id = :list_id';
        $params[':list_id'] = (int) $body['list_id'];
    }
    if (isset($body['position'])) {
        $fields[] = 'position = :position';
        $params[':position'] = (int) $body['position'];
    }

    if (!empty($fields)) {
        $pdo->prepare('UPDATE board_cards SET ' . implode(', ', $fields) . ' WHERE id = :id')
            ->execute($params);
    }

    // Replace card assignments if provided
    if (isset($body['assigned_ids']) && is_array($body['assigned_ids'])) {
        $pdo->prepare('DELETE FROM card_assignments WHERE card_id = :cid')
            ->execute([':cid' => $id]);
        $asgn = $pdo->prepare(
            'INSERT IGNORE INTO card_assignments (card_id, staff_id) VALUES (:cid, :sid)'
        );
        foreach ($body['assigned_ids'] as $sid) {
            $asgn->execute([':cid' => $id, ':sid' => (int) $sid]);
        }
    }

    jsonSuccess('Card updated.');
}

/* ── DELETE ───────────────────────────────────────────────── */
if ($method === 'DELETE') {
    $id = getQueryInt('id');
    if (!$id) {
        jsonError('Card ID is required.', 400);
    }
    $pdo->prepare('DELETE FROM board_cards WHERE id = :id')->execute([':id' => $id]);
    jsonSuccess('Card deleted.');
}

jsonError('Method not allowed.', 405);
