<?php
/**
 * JSM Digital — Application Configuration
 */

// ── Environment ──────────────────────────────────────────────
define('APP_ENV', getenv('APP_ENV') ?: 'production'); // 'development' | 'production'
define('APP_URL', getenv('APP_URL') ?: 'https://jsmdigital.com');

// ── Email ─────────────────────────────────────────────────────
define('MAIL_FROM_NAME',    'JSM Digital');
define('MAIL_FROM_ADDRESS', getenv('MAIL_FROM') ?: 'no-reply@jsmdigital.com');
define('MAIL_TO_ADDRESS',   getenv('MAIL_TO')   ?: 'hello@jsmdigital.com');

// ── Rate Limiting ─────────────────────────────────────────────
define('RATE_LIMIT_REQUESTS', 5);    // max requests…
define('RATE_LIMIT_WINDOW',  300);   // …per N seconds per IP

// ── Allowed Origins (CORS) ────────────────────────────────────
define('ALLOWED_ORIGINS', [
    'http://localhost:5173',
    'http://localhost:8000',
    APP_URL,
]);
