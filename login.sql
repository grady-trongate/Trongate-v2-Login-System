-- ============================================================
-- Trongate v2 Login System — Database Schema
-- ============================================================
-- Creates the framework tables and the members table needed
-- for the member authentication system.
-- ============================================================

-- ── User Levels ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS trongate_user_levels (
    id int(11) NOT NULL AUTO_INCREMENT,
    level_title varchar(125) DEFAULT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO trongate_user_levels (id, level_title) VALUES
(1, 'Administrators'),
(2, 'Members');

-- ── Central User Registry ──────────────────────────────────
CREATE TABLE IF NOT EXISTS trongate_users (
    id int(11) NOT NULL AUTO_INCREMENT,
    code varchar(32) DEFAULT NULL,
    user_level_id int(11) DEFAULT 0,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Session Tokens ─────────────────────────────────────────
CREATE TABLE IF NOT EXISTS trongate_tokens (
    id int(11) NOT NULL AUTO_INCREMENT,
    token varchar(125) DEFAULT NULL,
    user_id int(11) DEFAULT 0,
    expiry_date int(11) DEFAULT NULL,
    code varchar(3) DEFAULT '0',
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Administrators (built-in with framework) ──────────────
CREATE TABLE IF NOT EXISTS trongate_administrators (
    id int(11) NOT NULL AUTO_INCREMENT,
    url_string varchar(255) DEFAULT NULL,
    username varchar(55) DEFAULT NULL,
    email varchar(255) DEFAULT NULL,
    password varchar(255) NOT NULL,
    date_created int(11) NOT NULL,
    code varchar(16) NOT NULL,
    trongate_user_id int(11) NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Members ────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS members (
    id int(11) NOT NULL AUTO_INCREMENT,
    username varchar(55) DEFAULT NULL UNIQUE,
    email_address varchar(255) DEFAULT NULL,
    password varchar(255) NOT NULL,
    first_name text DEFAULT NULL,
    last_name text DEFAULT NULL,
    date_created int(11) NOT NULL,
    num_logins int(11) NOT NULL DEFAULT 0,
    last_login int(11) NOT NULL DEFAULT 0,
    trongate_user_id int(11) NOT NULL,
    code varchar(16) NOT NULL,
    confirmed tinyint(1) DEFAULT NULL,
    ip_address varchar(65) NOT NULL DEFAULT '',
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;