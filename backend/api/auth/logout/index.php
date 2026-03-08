<?php
/**
 * JSM Digital — POST /api/auth/logout
 *
 * Invalidates the current Bearer token.
 */

require_once __DIR__ . '/../../../helpers/functions.php';

setDashboardCorsHeaders();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Method not allowed.', 405);
}

$token = getBearerToken();
if ($token && preg_match('/^[0-9a-f]{64}$/', $token)) {
    $pdo = getDbConnection();
    $pdo->prepare('DELETE FROM staff_sessions WHERE token = :token')
        ->execute([':token' => $token]);
}

jsonSuccess('Logged out successfully.');
