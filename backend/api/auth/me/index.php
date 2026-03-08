<?php
/**
 * JSM Digital — GET /api/auth/me
 *
 * Returns the currently authenticated staff user's profile.
 */

require_once __DIR__ . '/../../../helpers/functions.php';

setDashboardCorsHeaders();

$user = requireAuth();

jsonSuccess('OK', ['user' => $user]);
