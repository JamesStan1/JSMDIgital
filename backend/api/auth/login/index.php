<?php
/**
 * JSM Digital — POST /api/auth/login
 *
 * Authenticates a staff member and returns a Bearer token.
 */

require_once __DIR__ . '/../../../helpers/functions.php';

setDashboardCorsHeaders();
ensureSchema();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Method not allowed.', 405);
}

$body     = getRequestBody();
$username = cleanString($body['username'] ?? '', 100);
$password = $body['password'] ?? '';

if ($username === '' || $password === '') {
    jsonError('Username and password are required.', 422);
}

$pdo  = getDbConnection();
$stmt = $pdo->prepare(
    'SELECT id, username, email, full_name, role, password
       FROM staff_users
      WHERE (username = :u1 OR email = :u2)
        AND is_active = 1
      LIMIT 1'
);
$stmt->execute([':u1' => $username, ':u2' => $username]);
$user = $stmt->fetch();

// Constant-time comparison to prevent username enumeration
if (!$user || !password_verify($password, $user['password'])) {
    usleep(random_int(100000, 300000));
    jsonError('Invalid credentials.', 401);
}

// Generate a cryptographically secure 64-char hex token
$token     = bin2hex(random_bytes(32));
$expiresAt = date('Y-m-d H:i:s', strtotime('+8 hours'));

$ins = $pdo->prepare(
    'INSERT INTO staff_sessions (user_id, token, expires_at)
     VALUES (:uid, :token, :exp)'
);
$ins->execute([':uid' => $user['id'], ':token' => $token, ':exp' => $expiresAt]);

// Remove expired sessions for this user (housekeeping)
$pdo->prepare('DELETE FROM staff_sessions WHERE user_id = :uid AND expires_at < NOW()')
    ->execute([':uid' => $user['id']]);

jsonSuccess('Login successful.', [
    'token' => $token,
    'user'  => [
        'id'        => (int) $user['id'],
        'username'  => $user['username'],
        'email'     => $user['email'],
        'full_name' => $user['full_name'],
        'role'      => $user['role'],
    ],
]);
