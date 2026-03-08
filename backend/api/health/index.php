<?php
/**
 * JSM Digital — Health-check endpoint
 * GET /api/health → { "status": "ok" }
 */
header('Content-Type: application/json; charset=UTF-8');
http_response_code(200);
echo json_encode(['status' => 'ok', 'service' => 'JSM Digital API', 'timestamp' => date('c')]);
