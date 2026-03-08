<?php
/**
 * JSM Digital — /api/dashboard/inquiries
 *
 * GET    — list inquiries (paginated, filterable by status)
 * PATCH  — update inquiry status  (?id=N)
 */

require_once __DIR__ . '/../../../helpers/functions.php';

setDashboardCorsHeaders();
$user = requireAuth();

$pdo    = getDbConnection();
$method = $_SERVER['REQUEST_METHOD'];

/* ── GET — list inquiries ──────────────────────────────────── */
if ($method === 'GET') {
    $status          = cleanString($_GET['status'] ?? '', 20);
    $allowed_statuses = ['', 'new', 'in_review', 'responded'];
    if (!in_array($status, $allowed_statuses, true)) {
        $status = '';
    }

    $page   = max(1, getQueryInt('page', 1));
    $limit  = 20;
    $offset = ($page - 1) * $limit;

    $where  = $status !== '' ? 'WHERE i.status = :status' : '';
    $params = $status !== '' ? [':status' => $status] : [];

    // Total count
    $cntStmt = $pdo->prepare("SELECT COUNT(*) FROM inquiries i $where");
    $cntStmt->execute($params);
    $total = (int) $cntStmt->fetchColumn();

    // Paginated list with assignment info
    $params[':limit']  = $limit;
    $params[':offset'] = $offset;

    $stmt = $pdo->prepare(
        "SELECT i.*,
                u.full_name  AS assigned_to_name,
                u.id         AS assigned_to_id,
                ab.full_name AS assigned_by_name
           FROM inquiries i
           LEFT JOIN inquiry_assignments ia ON ia.inquiry_id = i.id
           LEFT JOIN staff_users u  ON u.id  = ia.staff_id
           LEFT JOIN staff_users ab ON ab.id = ia.assigned_by
          $where
          ORDER BY i.created_at DESC
          LIMIT :limit OFFSET :offset"
    );
    $stmt->execute($params);
    $rows = $stmt->fetchAll();

    jsonSuccess('OK', [
        'inquiries' => $rows,
        'total'     => $total,
        'page'      => $page,
        'pages'     => (int) ceil($total / $limit),
    ]);
}

/* ── PATCH — update status ─────────────────────────────────── */
if ($method === 'PATCH') {
    $id = getQueryInt('id');
    if (!$id) {
        jsonError('Inquiry ID required.', 400);
    }

    $body   = getRequestBody();
    $status = cleanString($body['status'] ?? '', 20);
    $allowed = ['new', 'in_review', 'responded'];
    if (!in_array($status, $allowed, true)) {
        jsonError('Invalid status value.', 422);
    }

    $stmt = $pdo->prepare('UPDATE inquiries SET status = :status WHERE id = :id');
    $stmt->execute([':status' => $status, ':id' => $id]);

    jsonSuccess('Status updated.');
}

jsonError('Method not allowed.', 405);
