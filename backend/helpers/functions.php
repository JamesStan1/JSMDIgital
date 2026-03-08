<?php
/**
 * JSM Digital — Shared helper functions
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

/* ─────────────────────────────────────────────────────────────
 * CORS & JSON headers
 * ───────────────────────────────────────────────────────────── */
function setCorsHeaders(): void
{
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    $allowed = ALLOWED_ORIGINS;

    if (in_array($origin, $allowed, true)) {
        header('Access-Control-Allow-Origin: ' . $origin);
    }

    header('Access-Control-Allow-Methods: POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    header('Access-Control-Max-Age: 3600');
    header('Content-Type: application/json; charset=UTF-8');
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(204);
        exit;
    }
}

/* ─────────────────────────────────────────────────────────────
 * Response helpers
 * ───────────────────────────────────────────────────────────── */
function jsonSuccess(string $message, array $data = []): void
{
    http_response_code(200);
    echo json_encode(array_merge(['success' => true, 'message' => $message], $data));
    exit;
}

function jsonError(string $message, int $code = 400, array $errors = []): void
{
    http_response_code($code);
    $payload = ['success' => false, 'message' => $message];
    if (!empty($errors)) {
        $payload['errors'] = $errors;
    }
    echo json_encode($payload);
    exit;
}

/* ─────────────────────────────────────────────────────────────
 * Input / validation helpers
 * ───────────────────────────────────────────────────────────── */

/**
 * Decode the incoming JSON body once; returns an array or aborts.
 */
function getJsonBody(): array
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        jsonError('Method not allowed.', 405);
    }

    $raw = file_get_contents('php://input');
    if ($raw === false || $raw === '') {
        jsonError('Empty request body.', 400);
    }

    $data = json_decode($raw, true);
    if (!is_array($data)) {
        jsonError('Invalid JSON payload.', 400);
    }

    return $data;
}

/**
 * Sanitise a string: strip HTML tags, trim whitespace.
 * Returns an empty string if the value is missing or not a string.
 */
function cleanString(mixed $value, int $maxLength = 1000): string
{
    if (!isset($value) || !is_string($value)) {
        return '';
    }
    return mb_substr(trim(strip_tags($value)), 0, $maxLength);
}

/**
 * Validate an email address. Uses filter_var plus an extra
 * regex check to reject addresses that filter_var may accept
 * but are not RFC-5321 compliant for practical use.
 */
function isValidEmail(string $email): bool
{
    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        return false;
    }
    // Reject unusually long addresses or those with consecutive dots
    if (strlen($email) > 320) {
        return false;
    }
    return true;
}

/**
 * Check whether a value is one of a fixed set of allowed strings.
 */
function isAllowedValue(string $value, array $allowed): bool
{
    return in_array($value, $allowed, true);
}

/* ─────────────────────────────────────────────────────────────
 * Rate limiting (DB-backed, per IP per endpoint)
 * ───────────────────────────────────────────────────────────── */
function getClientIp(): string
{
    // Only trust REMOTE_ADDR; proxy headers can be spoofed.
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

function checkRateLimit(string $endpoint): void
{
    $pdo      = getDbConnection();
    $ip       = getClientIp();
    $window   = RATE_LIMIT_WINDOW;
    $maxHits  = RATE_LIMIT_REQUESTS;

    // Count existing hits in the current window
    $stmt = $pdo->prepare(
        'SELECT COUNT(*) FROM rate_limit_log
          WHERE ip_address = :ip
            AND endpoint   = :ep
            AND hit_time  >= DATE_SUB(NOW(), INTERVAL :win SECOND)'
    );
    $stmt->execute([':ip' => $ip, ':ep' => $endpoint, ':win' => $window]);
    $count = (int) $stmt->fetchColumn();

    if ($count >= $maxHits) {
        jsonError('Too many requests. Please wait a few minutes before trying again.', 429);
    }

    // Record this hit
    $ins = $pdo->prepare(
        'INSERT INTO rate_limit_log (ip_address, endpoint) VALUES (:ip, :ep)'
    );
    $ins->execute([':ip' => $ip, ':ep' => $endpoint]);

    // Prune old records to keep the table small (best-effort)
    $pdo->exec("DELETE FROM rate_limit_log WHERE hit_time < DATE_SUB(NOW(), INTERVAL 3600 SECOND)");
}

/* ─────────────────────────────────────────────────────────────
 * Simple email dispatcher using PHP's mail()
 * ───────────────────────────────────────────────────────────── */
function sendMail(string $to, string $subject, string $htmlBody): bool
{
    $from    = MAIL_FROM_ADDRESS;
    $fromName = MAIL_FROM_NAME;
    $boundary = md5(uniqid((string)rand(), true));

    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "From: =?UTF-8?B?" . base64_encode($fromName) . "?= <{$from}>\r\n";
    $headers .= "Reply-To: {$from}\r\n";
    $headers .= "X-Mailer: JSM-Digital-Mailer/1.0\r\n";

    // Encode subject to handle non-ASCII characters safely
    $encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';

    return mail($to, $encodedSubject, $htmlBody, $headers);
}
