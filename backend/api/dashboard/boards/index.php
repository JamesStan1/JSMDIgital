<?php
/**
 * JSM Digital — /api/dashboard/boards
 *
 * GET    — list all boards
 * POST   — create a board (auto-creates To Do / In Progress / Done lists)
 * DELETE — delete a board (?id=N, admin/manager only)
 */

require_once __DIR__ . '/../../../helpers/functions.php';

setDashboardCorsHeaders();
$user = requireAuth();

$pdo    = getDbConnection();
$method = $_SERVER['REQUEST_METHOD'];

/* ── GET ──────────────────────────────────────────────────── */
if ($method === 'GET') {
    $stmt = $pdo->prepare(
        'SELECT b.*,
                u.full_name AS created_by_name,
                (SELECT COUNT(*)
                   FROM board_lists bl
                  WHERE bl.board_id = b.id) AS list_count,
                (SELECT COUNT(*)
                   FROM board_cards bc
                   JOIN board_lists bl2 ON bc.list_id = bl2.id
                  WHERE bl2.board_id = b.id) AS card_count
           FROM boards b
           JOIN staff_users u ON u.id = b.created_by
          ORDER BY b.created_at DESC'
    );
    $stmt->execute();
    $boards = $stmt->fetchAll();

    foreach ($boards as &$b) {
        $b['id']         = (int) $b['id'];
        $b['list_count'] = (int) $b['list_count'];
        $b['card_count'] = (int) $b['card_count'];
        $b['created_by'] = (int) $b['created_by'];
    }

    jsonSuccess('OK', ['boards' => $boards]);
}

/* ── POST ─────────────────────────────────────────────────── */
if ($method === 'POST') {
    $body        = getRequestBody();
    $title       = cleanString($body['title']       ?? '', 255);
    $description = cleanString($body['description'] ?? '', 1000);
    $color       = cleanString($body['color']       ?? '#4f46e5', 7);

    if ($title === '') {
        jsonError('Board title is required.', 422);
    }
    if (!preg_match('/^#[0-9a-fA-F]{6}$/', $color)) {
        $color = '#4f46e5';
    }

    $stmt = $pdo->prepare(
        'INSERT INTO boards (title, description, color, created_by)
         VALUES (:t, :d, :c, :u)'
    );
    $stmt->execute([
        ':t' => $title,
        ':d' => $description,
        ':c' => $color,
        ':u' => $user['id'],
    ]);
    $boardId = (int) $pdo->lastInsertId();

    // Auto-seed default lists
    $listStmt = $pdo->prepare(
        'INSERT INTO board_lists (board_id, title, position)
         VALUES (:bid, :t, :p)'
    );
    foreach ([['To Do', 0], ['In Progress', 1], ['Done', 2]] as [$t, $p]) {
        $listStmt->execute([':bid' => $boardId, ':t' => $t, ':p' => $p]);
    }

    jsonSuccess('Board created.', ['id' => $boardId]);
}

/* ── DELETE ───────────────────────────────────────────────── */
if ($method === 'DELETE') {
    requireRole($user, ['admin', 'manager']);
    $id = getQueryInt('id');
    if (!$id) {
        jsonError('Board ID is required.', 400);
    }
    $pdo->prepare('DELETE FROM boards WHERE id = :id')->execute([':id' => $id]);
    jsonSuccess('Board deleted.');
}

jsonError('Method not allowed.', 405);
