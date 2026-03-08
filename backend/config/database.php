<?php
/**
 * JSM Digital — Database Connection (PDO)
 *
 * Credentials are read from environment variables so they are
 * never hard-coded in the repository.
 *
 * Set these in your web-server's environment, a .env file loaded
 * by a bootstrap script, or an Apache/Nginx virtual-host config.
 */

function getDbConnection(): PDO
{
    static $pdo = null;

    if ($pdo !== null) {
        return $pdo;
    }

    $host    = getenv('DB_HOST')     ?: '127.0.0.1';
    $port    = getenv('DB_PORT')     ?: '3306';
    $dbname  = getenv('DB_NAME')     ?: 'jsm_digital';
    $user    = getenv('DB_USER')     ?: 'root';
    $pass    = getenv('DB_PASSWORD') ?: '';
    $charset = 'utf8mb4';

    $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
    } catch (PDOException $e) {
        // Do NOT expose the real error message to the client.
        error_log('[JSM Digital] DB connection failed: ' . $e->getMessage());
        http_response_code(503);
        echo json_encode(['success' => false, 'message' => 'Service temporarily unavailable.']);
        exit;
    }

    return $pdo;
}

/**
 * Ensures the required tables exist (run once on first request or
 * use as a lightweight migration runner).
 *
 * In production, prefer running dedicated migrations instead.
 */
function ensureSchema(): void
{
    $pdo = getDbConnection();

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS contacts (
            id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name        VARCHAR(255)  NOT NULL,
            email       VARCHAR(320)  NOT NULL,
            subject     VARCHAR(500)  NOT NULL,
            message     TEXT          NOT NULL,
            ip_address  VARCHAR(45)   NOT NULL DEFAULT '',
            created_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS inquiries (
            id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name         VARCHAR(255)   NOT NULL,
            email        VARCHAR(320)   NOT NULL,
            company      VARCHAR(255)   NOT NULL DEFAULT '',
            phone        VARCHAR(50)    NOT NULL DEFAULT '',
            project_type VARCHAR(100)   NOT NULL,
            budget       VARCHAR(100)   NOT NULL DEFAULT '',
            timeline     VARCHAR(100)   NOT NULL DEFAULT '',
            description  TEXT           NOT NULL,
            status       ENUM('new','in_review','responded') NOT NULL DEFAULT 'new',
            ip_address   VARCHAR(45)    NOT NULL DEFAULT '',
            created_at   DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS rate_limit_log (
            id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            ip_address VARCHAR(45)  NOT NULL,
            endpoint   VARCHAR(100) NOT NULL,
            hit_time   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_ip_endpoint (ip_address, endpoint, hit_time)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
}
