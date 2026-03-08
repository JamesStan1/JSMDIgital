<?php
/**
 * JSM Digital — GET /api/dashboard/staff
 *
 * Returns the list of active staff users (for assignment dropdowns etc.).
 */

require_once __DIR__ . '/../../../helpers/functions.php';

setDashboardCorsHeaders();
$user = requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonError('Method not allowed.', 405);
}

$pdo  = getDbConnection();
$stmt = $pdo->query(
    'SELECT id, username, full_name, email, role
       FROM staff_users
      WHERE is_active = 1
      ORDER BY full_name ASC'
);
$staff = $stmt->fetchAll();

// Cast id to int for clean JSON
foreach ($staff as &$s) {
    $s['id'] = (int) $s['id'];
}

jsonSuccess('OK', ['staff' => $staff]);
