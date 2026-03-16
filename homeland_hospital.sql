-- ============================================================
--  HOMELAND HOSPITAL — Database Setup
--  Import this in phpMyAdmin or run:
--    mysql -u root -p < homeland_hospital.sql
--
--  All passwords are stored as PLAIN TEXT.
--  Login.php supports both plain-text and bcrypt.
--  Test logins listed at the bottom of this file.
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Create and select the database
CREATE DATABASE IF NOT EXISTS `homeland_hospital`
  DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `homeland_hospital`;

-- ── Drop tables (safe order) ─────────────────────────────────
DROP TABLE IF EXISTS `appointments`;
DROP TABLE IF EXISTS `patients`;
DROP TABLE IF EXISTS `doctors`;

-- ── patients ────────────────────────────────────────────────
CREATE TABLE `patients` (
  `id`         INT(11)      NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(120) NOT NULL,
  `email`      VARCHAR(180) NOT NULL,
  `password`   VARCHAR(255) NOT NULL,
  `phone`      VARCHAR(30)  DEFAULT NULL,
  `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_patient_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── doctors ─────────────────────────────────────────────────
CREATE TABLE `doctors` (
  `id`         INT(11)      NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(120) NOT NULL,
  `email`      VARCHAR(180) NOT NULL,
  `password`   VARCHAR(255) NOT NULL,
  `specialty`  VARCHAR(120) NOT NULL,
  `phone`      VARCHAR(30)  DEFAULT NULL,
  `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_doctor_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── appointments ────────────────────────────────────────────
CREATE TABLE `appointments` (
  `id`               INT(11)      NOT NULL AUTO_INCREMENT,
  `patient_id`       INT(11)      NOT NULL,
  `doctor_id`        INT(11)      NOT NULL,
  `appointment_date` DATE         NOT NULL,
  `message`          TEXT         DEFAULT NULL,
  `status`           ENUM('pending','confirmed','cancelled','completed')
                                  NOT NULL DEFAULT 'pending',
  `created_at`       TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_patient` (`patient_id`),
  KEY `idx_doctor`  (`doctor_id`),
  CONSTRAINT `fk_appt_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_appt_doctor`  FOREIGN KEY (`doctor_id`)  REFERENCES `doctors`  (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ── Seed: Doctors (password = doctor123) ────────────────────
INSERT INTO `doctors` (`name`, `email`, `password`, `specialty`, `phone`) VALUES
('Sarah Mitchell',   'sarah.mitchell@homeland.com',   'doctor123', 'Cardiology',              '+1 (800) 555-0101'),
('James Okonkwo',    'james.okonkwo@homeland.com',    'doctor123', 'Neurology',               '+1 (800) 555-0102'),
('Priya Sharma',     'priya.sharma@homeland.com',     'doctor123', 'Pediatrics',              '+1 (800) 555-0103'),
('David Hernandez',  'david.hernandez@homeland.com',  'doctor123', 'Orthopedics',             '+1 (800) 555-0104'),
('Amina Yusuf',      'amina.yusuf@homeland.com',      'doctor123', 'Dermatology',             '+1 (800) 555-0105'),
('Robert Chen',      'robert.chen@homeland.com',      'doctor123', 'General Practice',        '+1 (800) 555-0106'),
('Fatima Al-Hassan', 'fatima.alhassan@homeland.com',  'doctor123', 'Obstetrics & Gynecology', '+1 (800) 555-0107'),
('Liam Tremblay',    'liam.tremblay@homeland.com',    'doctor123', 'Psychiatry',              '+1 (800) 555-0108');


-- ── Seed: Patients (password = patient123) ──────────────────
INSERT INTO `patients` (`name`, `email`, `password`, `phone`) VALUES
('Alice Johnson',  'alice.johnson@example.com',  'patient123', '+1 (555) 201-1001'),
('Brian Nakamura', 'brian.nakamura@example.com', 'patient123', '+1 (555) 201-1002'),
('Clara Mensah',   'clara.mensah@example.com',   'patient123', '+1 (555) 201-1003'),
('Daniel Brooks',  'daniel.brooks@example.com',  'patient123', '+1 (555) 201-1004'),
('Elena Vasquez',  'elena.vasquez@example.com',  'patient123', '+1 (555) 201-1005');


-- ── Seed: Appointments ──────────────────────────────────────
INSERT INTO `appointments` (`patient_id`, `doctor_id`, `appointment_date`, `message`, `status`) VALUES
(1, 1, DATE_ADD(CURDATE(), INTERVAL  2 DAY), 'Chest tightness after exercise.',       'confirmed'),
(2, 2, DATE_ADD(CURDATE(), INTERVAL  4 DAY), 'Follow-up for recurring migraines.',    'pending'),
(3, 3, DATE_ADD(CURDATE(), INTERVAL  6 DAY), 'Annual wellness check for my child.',   'confirmed'),
(4, 4, DATE_ADD(CURDATE(), INTERVAL  9 DAY), 'Knee pain when climbing stairs.',       'pending'),
(5, 5, DATE_ADD(CURDATE(), INTERVAL 13 DAY), 'Rash on forearm that has not cleared.', 'pending'),
(1, 6, DATE_ADD(CURDATE(), INTERVAL  1 DAY), 'General check-up and BP review.',       'confirmed'),
(3, 7, DATE_ADD(CURDATE(), INTERVAL  5 DAY), 'Prenatal check-up 20 weeks.',           'confirmed'),
(2, 8, DATE_ADD(CURDATE(), INTERVAL 20 DAY), 'Anxiety and sleep issues.',             'pending'),
(1, 6, DATE_SUB(CURDATE(), INTERVAL 30 DAY), 'Routine blood test.',                   'completed'),
(2, 1, DATE_SUB(CURDATE(), INTERVAL 45 DAY), 'Palpitations during work.',             'completed'),
(3, 3, DATE_SUB(CURDATE(), INTERVAL 60 DAY), 'Childhood vaccinations.',               'completed'),
(4, 2, DATE_SUB(CURDATE(), INTERVAL 20 DAY), 'Persistent headaches.',                 'completed'),
(5, 4, DATE_SUB(CURDATE(), INTERVAL 15 DAY), 'Post-surgery physiotherapy review.',    'completed');

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
--  TEST LOGIN CREDENTIALS
-- ============================================================
--
--  Go to: http://localhost/homeland_hospital/
--
--  PATIENT LOGIN  (password: patient123)
--  alice.johnson@example.com
--  brian.nakamura@example.com
--  clara.mensah@example.com
--  daniel.brooks@example.com
--  elena.vasquez@example.com
--
--  DOCTOR LOGIN  (password: doctor123)
--  sarah.mitchell@homeland.com
--  james.okonkwo@homeland.com
--  priya.sharma@homeland.com
--  david.hernandez@homeland.com
--  amina.yusuf@homeland.com
--  robert.chen@homeland.com
--  fatima.alhassan@homeland.com
--  liam.tremblay@homeland.com
--
-- ============================================================
