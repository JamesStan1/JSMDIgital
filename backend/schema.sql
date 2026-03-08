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

-- ── Staff & Authentication ────────────────────────────────────

CREATE TABLE IF NOT EXISTS staff_users (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(100) NOT NULL UNIQUE,
    email      VARCHAR(320) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    full_name  VARCHAR(255) NOT NULL,
    role       ENUM('admin','manager','staff') NOT NULL DEFAULT 'staff',
    is_active  TINYINT(1)   NOT NULL DEFAULT 1,
    created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS staff_sessions (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id    INT UNSIGNED NOT NULL,
    token      CHAR(64)     NOT NULL UNIQUE,
    expires_at DATETIME     NOT NULL,
    created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_token (token),
    INDEX idx_user (user_id),
    FOREIGN KEY (user_id) REFERENCES staff_users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Inquiry Assignments ───────────────────────────────────────

CREATE TABLE IF NOT EXISTS inquiry_assignments (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    inquiry_id  INT UNSIGNED NOT NULL UNIQUE,
    staff_id    INT UNSIGNED NOT NULL,
    assigned_by INT UNSIGNED NOT NULL,
    notes       TEXT,
    created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_inquiry (inquiry_id),
    INDEX idx_staff (staff_id),
    FOREIGN KEY (inquiry_id) REFERENCES inquiries(id) ON DELETE CASCADE,
    FOREIGN KEY (staff_id)   REFERENCES staff_users(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_by) REFERENCES staff_users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Task Boards ───────────────────────────────────────────────

CREATE TABLE IF NOT EXISTS boards (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(255) NOT NULL,
    description TEXT,
    color       CHAR(7)      NOT NULL DEFAULT '#4f46e5',
    created_by  INT UNSIGNED NOT NULL,
    created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES staff_users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS board_lists (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    board_id   INT UNSIGNED NOT NULL,
    title      VARCHAR(255) NOT NULL,
    position   SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_board (board_id),
    FOREIGN KEY (board_id) REFERENCES boards(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS board_cards (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    list_id     INT UNSIGNED NOT NULL,
    title       VARCHAR(500) NOT NULL,
    description TEXT,
    priority    ENUM('low','medium','high','urgent') NOT NULL DEFAULT 'medium',
    due_date    DATE,
    position    SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    created_by  INT UNSIGNED NOT NULL,
    created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_list (list_id),
    FOREIGN KEY (list_id)    REFERENCES board_lists(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES staff_users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS card_assignments (
    card_id  INT UNSIGNED NOT NULL,
    staff_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (card_id, staff_id),
    FOREIGN KEY (card_id)  REFERENCES board_cards(id) ON DELETE CASCADE,
    FOREIGN KEY (staff_id) REFERENCES staff_users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
