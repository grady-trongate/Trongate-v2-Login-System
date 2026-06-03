-- ============================================================
-- Trongate v2 Login System — Database Schema
-- ============================================================
-- Run this file to create all tables required by the
-- multi-level login system described in The Trongate Way.
-- ============================================================

-- ------------------------------------------------------------
-- User level definitions
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `trongate_user_levels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `level_title` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Shared user identity registry
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `trongate_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) DEFAULT NULL,
  `user_level_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Authentication tokens (sessions)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `trongate_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_level_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `expires_at` datetime DEFAULT NULL,
  `remember_me` tinyint(4) NOT NULL DEFAULT 0,
  `code` varchar(255) DEFAULT NULL,
  `expiry_date` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `token` (`token`),
  KEY `user_level_id` (`user_level_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Per-level table: Administrators (level 1)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `trongate_administrators` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `trongate_user_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT 1,
  `failed_login_attempts` int(11) NOT NULL DEFAULT 0,
  `last_failed_attempt` int(11) NOT NULL DEFAULT 0,
  `login_blocked_until` int(11) NOT NULL DEFAULT 0,
  `failed_login_ip` varchar(45) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Per-level table: Members (level 2)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `members` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `trongate_user_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email_address` varchar(255) NOT NULL,
  `pword` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Insert default user levels
-- ------------------------------------------------------------
INSERT INTO `trongate_user_levels` (`id`, `level_title`) VALUES
(1, 'Administrator'),
(2, 'Member')
ON DUPLICATE KEY UPDATE `level_title` = VALUES(`level_title`);
