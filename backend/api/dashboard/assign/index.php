<?php
/**
 * JSM Digital — POST /api/dashboard/assign
 *
 * Assigns (or re-assigns) an inquiry to a staff member.
 * Requires admin or manager role.
 */

require_once __DIR__ . '/../../../helpers/functions.php';

setDashboardCorsHeaders();
$user = requireAuth();
requireRole($user, ['admin', 'manager']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Method not allowed.', 405);
}

$body       = getRequestBody();
$inquiryId  = (int) ($body['inquiry_id'] ?? 0);
$staffId    = (int) ($body['staff_id']   ?? 0);
$notes      = cleanString($body['notes'] ?? '', 1000);

if (!$inquiryId || !$staffId) {
    jsonError('inquiry_id and staff_id are required.', 422);
}

$pdo = getDbConnection();

// Verify inquiry exists
$chk = $pdo->prepare('SELECT id FROM inquiries WHERE id = :id');
$chk->execute([':id' => $inquiryId]);
if (!$chk->fetch()) {
    jsonError('Inquiry not found.', 404);
}

// Verify staff member exists and is active
$chk = $pdo->prepare('SELECT id FROM staff_users WHERE id = :id AND is_active = 1');
$chk->execute([':id' => $staffId]);
if (!$chk->fetch()) {
    jsonError('Staff member not found.', 404);
}

// Upsert assignment (replace any existing assignment for this inquiry)
$del = $pdo->prepare('DELETE FROM inquiry_assignments WHERE inquiry_id = :iid');
$del->execute([':iid' => $inquiryId]);

$ins = $pdo->prepare(
    'INSERT INTO inquiry_assignments (inquiry_id, staff_id, assigned_by, notes)
     VALUES (:iid, :sid, :bid, :notes)'
);
$ins->execute([
    ':iid'   => $inquiryId,
    ':sid'   => $staffId,
    ':bid'   => $user['id'],
    ':notes' => $notes,
]);

jsonSuccess('Inquiry assigned successfully.');
