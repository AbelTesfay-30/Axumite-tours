-- Run this once to set up the database
CREATE DATABASE IF NOT EXISTS axumite_tours
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE axumite_tours;

CREATE TABLE IF NOT EXISTS bookings (
  id          INT           NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(100)  NOT NULL,
  email       VARCHAR(150)  NOT NULL,
  phone       VARCHAR(30)   DEFAULT NULL,
  destination VARCHAR(100)  DEFAULT NULL,
  date        DATE          DEFAULT NULL,
  guests      INT           DEFAULT 1,
  message     TEXT          NOT NULL,
  created_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
