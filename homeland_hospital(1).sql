-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 23, 2026 at 07:51 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `homeland_hospital`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `appointment_date` date NOT NULL,
  `message` text DEFAULT NULL,
  `status` enum('pending','confirmed','cancelled','completed') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `patient_id`, `doctor_id`, `appointment_date`, `message`, `status`, `created_at`) VALUES
(1, 1, 1, '2026-03-13', 'Chest tightness after exercise.', 'confirmed', '2026-03-11 18:35:56'),
(2, 2, 2, '2026-03-15', 'Follow-up for recurring migraines.', 'pending', '2026-03-11 18:35:56'),
(3, 3, 3, '2026-03-17', 'Annual wellness check for my child.', 'confirmed', '2026-03-11 18:35:56'),
(4, 4, 4, '2026-03-20', 'Knee pain when climbing stairs.', 'pending', '2026-03-11 18:35:56'),
(5, 5, 5, '2026-03-24', 'Rash on forearm that has not cleared.', 'pending', '2026-03-11 18:35:56'),
(6, 1, 6, '2026-03-12', 'General check-up and BP review.', 'confirmed', '2026-03-11 18:35:56'),
(7, 3, 7, '2026-03-16', 'Prenatal check-up 20 weeks.', 'confirmed', '2026-03-11 18:35:56'),
(8, 2, 8, '2026-03-31', 'Anxiety and sleep issues.', 'pending', '2026-03-11 18:35:56'),
(9, 1, 6, '2026-02-09', 'Routine blood test.', 'completed', '2026-03-11 18:35:56'),
(10, 2, 1, '2026-01-25', 'Palpitations during work.', 'completed', '2026-03-11 18:35:56'),
(11, 3, 3, '2026-01-10', 'Childhood vaccinations.', 'completed', '2026-03-11 18:35:56'),
(12, 4, 2, '2026-02-19', 'Persistent headaches.', 'completed', '2026-03-11 18:35:56'),
(13, 5, 4, '2026-02-24', 'Post-surgery physiotherapy review.', 'completed', '2026-03-11 18:35:56'),
(14, 6, 2, '2026-12-04', 'leg pain', 'pending', '2026-03-11 22:05:54'),
(15, 7, 2, '2026-03-20', 'back pain', 'pending', '2026-03-12 12:10:34');

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `id` int(11) NOT NULL,
  `name` varchar(120) NOT NULL,
  `email` varchar(180) NOT NULL,
  `password` varchar(255) NOT NULL,
  `specialty` varchar(120) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`id`, `name`, `email`, `password`, `specialty`, `phone`, `created_at`) VALUES
(1, 'Sarah Mitchell', 'sarah.mitchell@homeland.com', 'doctor123', 'Cardiology', '+1 (800) 555-0101', '2026-03-11 18:35:56'),
(2, 'James Okonkwo', 'james.okonkwo@homeland.com', 'doctor123', 'Neurology', '+1 (800) 555-0102', '2026-03-11 18:35:56'),
(3, 'Priya Sharma', 'priya.sharma@homeland.com', 'doctor123', 'Pediatrics', '+1 (800) 555-0103', '2026-03-11 18:35:56'),
(4, 'David Hernandez', 'david.hernandez@homeland.com', 'doctor123', 'Orthopedics', '+1 (800) 555-0104', '2026-03-11 18:35:56'),
(5, 'Amina Yusuf', 'amina.yusuf@homeland.com', 'doctor123', 'Dermatology', '+1 (800) 555-0105', '2026-03-11 18:35:56'),
(6, 'Robert Chen', 'robert.chen@homeland.com', 'doctor123', 'General Practice', '+1 (800) 555-0106', '2026-03-11 18:35:56'),
(7, 'Fatima Al-Hassan', 'fatima.alhassan@homeland.com', 'doctor123', 'Obstetrics & Gynecology', '+1 (800) 555-0107', '2026-03-11 18:35:56'),
(8, 'Liam Tremblay', 'liam.tremblay@homeland.com', 'doctor123', 'Psychiatry', '+1 (800) 555-0108', '2026-03-11 18:35:56');

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `id` int(11) NOT NULL,
  `name` varchar(120) NOT NULL,
  `email` varchar(180) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`id`, `name`, `email`, `password`, `phone`, `created_at`) VALUES
(1, 'Alice Johnson', 'alice.johnson@example.com', 'patient123', '+1 (555) 201-1001', '2026-03-11 18:35:56'),
(2, 'Brian Nakamura', 'brian.nakamura@example.com', 'patient123', '+1 (555) 201-1002', '2026-03-11 18:35:56'),
(3, 'Clara Mensah', 'clara.mensah@example.com', 'patient123', '+1 (555) 201-1003', '2026-03-11 18:35:56'),
(4, 'Daniel Brooks', 'daniel.brooks@example.com', 'patient123', '+1 (555) 201-1004', '2026-03-11 18:35:56'),
(5, 'Elena Vasquez', 'elena.vasquez@example.com', 'patient123', '+1 (555) 201-1005', '2026-03-11 18:35:56'),
(6, 'musisi genesis', 'musisigenesis@example.com', '$2y$10$eVQ9G0EOBRpqHEgwpZP.3uvT0mLodspwfRJgF81i.2IKYN4o4IpZi', '0749363828', '2026-03-11 21:59:32'),
(7, 'mark joseph', 'joseph@gmail.com', '$2y$10$tW1gMzFL1Tv42GKkr9wicuHyenRJ0TST6cR7zLHAQhU7IpijNMdw.', '0754144827', '2026-03-12 12:03:47'),
(8, 'nakijoba malaika', 'nakijobamalaika@uinik.com', '$2y$10$.kbUeV07TAUvVhJJRiHj3Oy3vlYZ.SSK.rzkP8PMCjiz8S1ZsaN4e', '0700000000', '2026-03-17 13:10:33');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_content`
--

CREATE TABLE `tbl_content` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_content`
--

INSERT INTO `tbl_content` (`id`, `title`, `description`, `image_url`, `category`, `created_at`) VALUES
(1, 'Welcome to Homeland Hospital', 'Homeland Hospital is a leading healthcare provider committed to delivering compassionate and expert medical care to every patient who walks through our doors.', 'https://images.unsplash.com/photo-1586773860418-d37222d8fce3?w=800&auto=format&fit=crop&q=70', 'General', '2026-03-21 18:08:11'),
(2, 'Cardiology Department', 'Our Cardiology unit is equipped with the latest diagnostic tools and staffed by experienced heart specialists offering comprehensive cardiac screening and treatment.', 'https://images.unsplash.com/photo-1551190822-a9333d879b1f?w=800&auto=format&fit=crop&q=70', 'Department', '2026-03-21 18:08:11'),
(3, 'Pediatric Care Services', 'We provide gentle and child-friendly medical care for infants, children, and teenagers. Our pediatric team ensures every young patient feels safe and comfortable.', 'https://images.unsplash.com/photo-1579684385127-1ef15d508118?w=800&auto=format&fit=crop&q=70', 'Department', '2026-03-21 18:08:11'),
(4, 'Mental Health and Psychiatry', 'Homeland Hospital offers confidential mental health support including therapy, counselling, and psychiatric evaluation for patients of all ages.', 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?w=800&auto=format&fit=crop&q=70', 'Department', '2026-03-21 18:08:11'),
(5, 'Book Your Appointment Today', 'Scheduling a visit with one of our doctors has never been easier. Log in to the patient portal, choose your preferred doctor and date, and confirm in minutes.', 'https://images.unsplash.com/photo-1631217868264-e5b90bb7e133?w=800&auto=format&fit=crop&q=70', 'General', '2026-03-21 18:08:11');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_patient` (`patient_id`),
  ADD KEY `idx_doctor` (`doctor_id`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_doctor_email` (`email`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_patient_email` (`email`);

--
-- Indexes for table `tbl_content`
--
ALTER TABLE `tbl_content`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tbl_content`
--
ALTER TABLE `tbl_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `fk_appt_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_appt_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
