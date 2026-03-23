-- phpMyAdmin SQL Dump
-- Homeland Hospital Database
-- Import this entire file into phpMyAdmin to set up the database.

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- ============================================================
-- Table: appointments
-- ============================================================
CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `appointment_date` date NOT NULL,
  `message` text DEFAULT NULL,
  `status` enum('pending','confirmed','cancelled','completed') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `appointments` VALUES
(1,1,1,'2026-03-13','Chest tightness after exercise.','confirmed','2026-03-11 18:35:56'),
(2,2,2,'2026-03-15','Follow-up for recurring migraines.','pending','2026-03-11 18:35:56'),
(3,3,3,'2026-03-17','Annual wellness check for my child.','confirmed','2026-03-11 18:35:56'),
(4,4,4,'2026-03-20','Knee pain when climbing stairs.','pending','2026-03-11 18:35:56'),
(5,5,5,'2026-03-24','Rash on forearm that has not cleared.','pending','2026-03-11 18:35:56'),
(6,1,6,'2026-03-12','General check-up and BP review.','confirmed','2026-03-11 18:35:56'),
(7,3,7,'2026-03-16','Prenatal check-up 20 weeks.','confirmed','2026-03-11 18:35:56'),
(8,2,8,'2026-03-31','Anxiety and sleep issues.','pending','2026-03-11 18:35:56'),
(9,1,6,'2026-02-09','Routine blood test.','completed','2026-03-11 18:35:56'),
(10,2,1,'2026-01-25','Palpitations during work.','completed','2026-03-11 18:35:56'),
(11,3,3,'2026-01-10','Childhood vaccinations.','completed','2026-03-11 18:35:56'),
(12,4,2,'2026-02-19','Persistent headaches.','completed','2026-03-11 18:35:56'),
(13,5,4,'2026-02-24','Post-surgery physiotherapy review.','completed','2026-03-11 18:35:56'),
(14,6,2,'2026-12-04','leg pain','pending','2026-03-11 22:05:54'),
(15,7,2,'2026-03-20','back pain','pending','2026-03-12 12:10:34');

-- ============================================================
-- Table: doctors
-- ============================================================
CREATE TABLE `doctors` (
  `id` int(11) NOT NULL,
  `name` varchar(120) NOT NULL,
  `email` varchar(180) NOT NULL,
  `password` varchar(255) NOT NULL,
  `specialty` varchar(120) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `doctors` VALUES
(1,'Sarah Mitchell','sarah.mitchell@homeland.com','doctor123','Cardiology','+1 (800) 555-0101','2026-03-11 18:35:56'),
(2,'James Okonkwo','james.okonkwo@homeland.com','doctor123','Neurology','+1 (800) 555-0102','2026-03-11 18:35:56'),
(3,'Priya Sharma','priya.sharma@homeland.com','doctor123','Pediatrics','+1 (800) 555-0103','2026-03-11 18:35:56'),
(4,'David Hernandez','david.hernandez@homeland.com','doctor123','Orthopedics','+1 (800) 555-0104','2026-03-11 18:35:56'),
(5,'Amina Yusuf','amina.yusuf@homeland.com','doctor123','Dermatology','+1 (800) 555-0105','2026-03-11 18:35:56'),
(6,'Robert Chen','robert.chen@homeland.com','doctor123','General Practice','+1 (800) 555-0106','2026-03-11 18:35:56'),
(7,'Fatima Al-Hassan','fatima.alhassan@homeland.com','doctor123','Obstetrics & Gynecology','+1 (800) 555-0107','2026-03-11 18:35:56'),
(8,'Liam Tremblay','liam.tremblay@homeland.com','doctor123','Psychiatry','+1 (800) 555-0108','2026-03-11 18:35:56');

-- ============================================================
-- Table: patients
-- ============================================================
CREATE TABLE `patients` (
  `id` int(11) NOT NULL,
  `name` varchar(120) NOT NULL,
  `email` varchar(180) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `patients` VALUES
(1,'Alice Johnson','alice.johnson@example.com','patient123','+1 (555) 201-1001','2026-03-11 18:35:56'),
(2,'Brian Nakamura','brian.nakamura@example.com','patient123','+1 (555) 201-1002','2026-03-11 18:35:56'),
(3,'Clara Mensah','clara.mensah@example.com','patient123','+1 (555) 201-1003','2026-03-11 18:35:56'),
(4,'Daniel Brooks','daniel.brooks@example.com','patient123','+1 (555) 201-1004','2026-03-11 18:35:56'),
(5,'Elena Vasquez','elena.vasquez@example.com','patient123','+1 (555) 201-1005','2026-03-11 18:35:56'),
(6,'musisi genesis','musisigenesis@example.com','$2y$10$eVQ9G0EOBRpqHEgwpZP.3uvT0mLodspwfRJgF81i.2IKYN4o4IpZi','0749363828','2026-03-11 21:59:32'),
(7,'mark joseph','joseph@gmail.com','$2y$10$tW1gMzFL1Tv42GKkr9wicuHyenRJ0TST6cR7zLHAQhU7IpijNMdw.','0754144827','2026-03-12 12:03:47');

-- ============================================================
-- NEW TABLE: tbl_content
--
-- PURPOSE: Stores hospital services shown on services.php.
-- WHY THIS APPROACH: Instead of writing a hard-coded <div>
--   block for every service, services.php fetches rows from
--   this table and injects them into ONE card template using
--   a PHP while loop. To add a new service, just INSERT a
--   row here in phpMyAdmin — the page updates automatically.
--
-- COLUMNS:
--   id          Primary key, auto-increments
--   title       Card heading  (e.g. "Cardiology")
--   description Body text     (short paragraph about the service)
--   image_url   Card image    (full URL or relative path)
--   category    Badge label   (e.g. "Surgical", "Diagnostic")
--   created_at  Row timestamp (set automatically)
-- ============================================================
CREATE TABLE `tbl_content` (
  `id`          int(11)      NOT NULL AUTO_INCREMENT,
  `title`       varchar(150) NOT NULL,
  `description` text         NOT NULL,
  `image_url`   varchar(500) DEFAULT NULL,
  `category`    varchar(80)  DEFAULT NULL,
  `created_at`  timestamp    NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample rows — add more here or via phpMyAdmin to test the dynamic loop
INSERT INTO `tbl_content` (`id`,`title`,`description`,`image_url`,`category`) VALUES
(1,'Cardiology','Our cardiology unit provides comprehensive heart care including ECG, echocardiography, stress testing, and management of hypertension, heart failure, and arrhythmias. Our specialists are available 24/7 for cardiac emergencies.','https://images.unsplash.com/photo-1628348068343-c6a848d2b6dd?w=600&auto=format&fit=crop&q=70','Specialist'),
(2,'Neurology','We diagnose and treat disorders of the brain, spinal cord, and nervous system including epilepsy, stroke, migraine, and Parkinson\'s disease. Advanced imaging and neurophysiology services are on-site.','https://images.unsplash.com/photo-1559757175-0eb30cd8c063?w=600&auto=format&fit=crop&q=70','Specialist'),
(3,'Pediatrics','Dedicated child healthcare from newborns to adolescents. Services include immunisations, growth monitoring, acute illness management, and developmental assessments in a child-friendly environment.','https://images.unsplash.com/photo-1579684385127-1ef15d508118?w=600&auto=format&fit=crop&q=70','Family'),
(4,'Orthopedics','Surgical and non-surgical treatment of musculoskeletal conditions including fractures, joint replacements, sports injuries, and spine disorders. Physiotherapy supports post-operative rehabilitation.','https://images.unsplash.com/photo-1530026405186-ed1f139313f8?w=600&auto=format&fit=crop&q=70','Surgical'),
(5,'Dermatology','Diagnosis and treatment of skin, hair, and nail conditions including acne, eczema, psoriasis, and skin cancer screening. Laser and light therapy equipment available on-site.','https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?w=600&auto=format&fit=crop&q=70','Specialist'),
(6,'General Practice','Your first point of contact for all health concerns. Our GPs handle preventive care, chronic disease management, minor procedures, and referrals. Walk-in and same-day appointments available.','https://images.unsplash.com/photo-1666214280391-8ff5bd3c0bf0?w=600&auto=format&fit=crop&q=70','Primary'),
(7,'Obstetrics & Gynaecology','Complete women\'s health services including antenatal care, safe delivery, postnatal support, family planning, and management of gynaecological conditions. Private and shared maternity rooms available.','https://images.unsplash.com/photo-1638202993928-7d113b8e4519?w=600&auto=format&fit=crop&q=70','Family'),
(8,'Psychiatry & Mental Health','Confidential assessment and treatment of mental health conditions including depression, anxiety, PTSD, and substance use disorders. Outpatient therapy and inpatient stabilisation available.','https://images.unsplash.com/photo-1493836512294-502baa1986e2?w=600&auto=format&fit=crop&q=70','Specialist'),
(9,'Diagnostic Imaging','Full radiology suite including X-ray, ultrasound, CT scan, and MRI. Reports reviewed by board-certified radiologists; results available within 24 hours for non-urgent cases.','https://images.unsplash.com/photo-1516069677018-378515003435?w=600&auto=format&fit=crop&q=70','Diagnostic'),
(10,'Laboratory Services','On-site pathology laboratory offering blood counts, metabolic panels, hormone assays, microbiology cultures, and histopathology. Urgent processing available around the clock.','https://images.unsplash.com/photo-1576086213369-97a306d36557?w=600&auto=format&fit=crop&q=70','Diagnostic');

-- ============================================================
-- Indexes
-- ============================================================
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_patient` (`patient_id`),
  ADD KEY `idx_doctor` (`doctor_id`);

ALTER TABLE `doctors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_doctor_email` (`email`);

ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_patient_email` (`email`);

-- ============================================================
-- AUTO_INCREMENT
-- ============================================================
ALTER TABLE `appointments` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
ALTER TABLE `doctors`      MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
ALTER TABLE `patients`     MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
ALTER TABLE `tbl_content`  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

-- ============================================================
-- Foreign key constraints
-- ============================================================
ALTER TABLE `appointments`
  ADD CONSTRAINT `fk_appt_doctor`  FOREIGN KEY (`doctor_id`)  REFERENCES `doctors`  (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_appt_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
