<?php
/**
 * JSM Digital — /api/dashboard/lists
 *
 * GET    — lists for a board  (?board_id=N)
 * POST   — create a list
 * DELETE — delete a list      (?id=N)
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
        'SELECT l.*,
                (SELECT COUNT(*) FROM board_cards c WHERE c.list_id = l.id) AS card_count
           FROM board_lists l
          WHERE l.board_id = :bid
          ORDER BY l.position ASC, l.id ASC'
    );
    $stmt->execute([':bid' => $boardId]);
    $lists = $stmt->fetchAll();

    foreach ($lists as &$l) {
        $l['id']         = (int) $l['id'];
        $l['board_id']   = (int) $l['board_id'];
        $l['position']   = (int) $l['position'];
        $l['card_count'] = (int) $l['card_count'];
    }

    jsonSuccess('OK', ['lists' => $lists]);
}

/* ── POST ─────────────────────────────────────────────────── */
if ($method === 'POST') {
    $body    = getRequestBody();
    $boardId = (int) ($body['board_id'] ?? 0);
    $title   = cleanString($body['title'] ?? '', 255);

    if (!$boardId || $title === '') {
        jsonError('board_id and title are required.', 422);
    }

    // Determine next position
    $pos = $pdo->prepare(
        'SELECT COALESCE(MAX(position), -1) + 1 FROM board_lists WHERE board_id = :bid'
    );
    $pos->execute([':bid' => $boardId]);
    $position = (int) $pos->fetchColumn();

    $stmt = $pdo->prepare(
        'INSERT INTO board_lists (board_id, title, position)
         VALUES (:bid, :t, :p)'
    );
    $stmt->execute([':bid' => $boardId, ':t' => $title, ':p' => $position]);
    $id = (int) $pdo->lastInsertId();

    jsonSuccess('List created.', [
        'id'       => $id,
        'title'    => $title,
        'position' => $position,
        'board_id' => $boardId,
    ]);
}

/* ── DELETE ───────────────────────────────────────────────── */
if ($method === 'DELETE') {
    $id = getQueryInt('id');
    if (!$id) {
        jsonError('List ID is required.', 400);
    }
    $pdo->prepare('DELETE FROM board_lists WHERE id = :id')->execute([':id' => $id]);
    jsonSuccess('List deleted.');
}

jsonError('Method not allowed.', 405);
