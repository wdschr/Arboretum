-- Arboretum: hostname & database directory
-- Run this once against an empty MySQL database (in cPanel or similar:
-- create a database and a user, then import this file via phpMyAdmin).
-- Only needed for a manual install - install.php does this for you.
-- See SETUP.md for the full walkthrough.

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS users (
  id                 INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username           VARCHAR(50) NOT NULL UNIQUE,
  password_hash      VARCHAR(255) NOT NULL,
  role               ENUM('admin','editor','viewer') NOT NULL DEFAULT 'viewer',
  active             TINYINT(1) NOT NULL DEFAULT 1,
  must_change_password TINYINT(1) NOT NULL DEFAULT 0,
  failed_attempts    TINYINT UNSIGNED NOT NULL DEFAULT 0,
  locked_until       DATETIME NULL,
  created_at         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  last_login_at      DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS hosts (
  id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  hostname     VARCHAR(100) NOT NULL UNIQUE,
  tier         ENUM('machine','webapp','service') NOT NULL,
  role         VARCHAR(150) NOT NULL DEFAULT '',
  runs         VARCHAR(150) NOT NULL DEFAULT '',
  status       ENUM('live','planned') NOT NULL DEFAULT 'live',
  ip           VARCHAR(45) NOT NULL DEFAULT '',
  port         VARCHAR(20) NOT NULL DEFAULT '',
  on_machine   VARCHAR(100) NOT NULL DEFAULT '',
  why          VARCHAR(255) NOT NULL DEFAULT '',
  notes        VARCHAR(255) NOT NULL DEFAULT '',
  created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  created_by   INT UNSIGNED NULL,
  updated_by   INT UNSIGNED NULL,
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
  FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS databases_directory (
  id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name         VARCHAR(100) NOT NULL UNIQUE,
  engine_name  VARCHAR(50) NOT NULL DEFAULT '',
  host_machine VARCHAR(100) NOT NULL DEFAULT '',
  port         VARCHAR(20) NOT NULL DEFAULT '',
  purpose      VARCHAR(150) NOT NULL DEFAULT '',
  status       ENUM('live','planned') NOT NULL DEFAULT 'live',
  notes        VARCHAR(255) NOT NULL DEFAULT '',
  created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  created_by   INT UNSIGNED NULL,
  updated_by   INT UNSIGNED NULL,
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
  FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS audit_log (
  id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id      INT UNSIGNED NULL,
  username     VARCHAR(50) NOT NULL,
  action       ENUM('create','update','delete') NOT NULL,
  entity_type  VARCHAR(20) NOT NULL,
  entity_label VARCHAR(150) NOT NULL,
  details      TEXT NULL,
  created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- No seed admin row is created here on purpose. Heart Internet shared
-- hosting usually has no SSH access, so the first admin account is created
-- through the browser instead: upload the app, visit setup.php once, and
-- it will let you create exactly one admin account for as long as the
-- users table is empty. See SETUP.md.
