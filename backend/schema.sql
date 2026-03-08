# SQL schema for JSM Digital backend
# Run this once to create the database and tables.
# Alternatively, let ensureSchema() in database.php auto-create them.

CREATE DATABASE IF NOT EXISTS jsm_digital
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE jsm_digital;

CREATE TABLE IF NOT EXISTS contacts (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(255)  NOT NULL,
    email       VARCHAR(320)  NOT NULL,
    subject     VARCHAR(500)  NOT NULL,
    message     TEXT          NOT NULL,
    ip_address  VARCHAR(45)   NOT NULL DEFAULT '',
    created_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
    created_at   DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_status (status),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS rate_limit_log (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45)  NOT NULL,
    endpoint   VARCHAR(100) NOT NULL,
    hit_time   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ip_endpoint (ip_address, endpoint, hit_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
