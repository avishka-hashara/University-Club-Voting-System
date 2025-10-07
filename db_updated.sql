-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 07, 2025 at 11:58 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `club_voting`
--

-- --------------------------------------------------------

--
-- Table structure for table `candidates`
--

CREATE TABLE `candidates` (
  `id` int(11) NOT NULL,
  `election_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `bio` text DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `candidates`
--

INSERT INTO `candidates` (`id`, `election_id`, `name`, `bio`, `photo`, `created_at`) VALUES
(23, 18, 'Janith Sadaken', 'SE Student', 'cand_68e4cc984062e2.90412579.png', '2025-10-07 08:17:28'),
(24, 18, 'Nirasha Fernando', 'CS student', 'cand_68e4ccd40e67f4.06321366.png', '2025-10-07 08:18:28'),
(25, 18, 'Saumya Bhashini', 'CE Student', 'cand_68e4cd176f02b4.38331616.png', '2025-10-07 08:19:35');

-- --------------------------------------------------------

--
-- Table structure for table `clubs`
--

CREATE TABLE `clubs` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `clubs`
--

INSERT INTO `clubs` (`id`, `name`, `description`) VALUES
(3, 'IEEE Student Branch', 'IEEE club'),
(4, 'Rotaract Club', 'Rotaract');

-- --------------------------------------------------------

--
-- Table structure for table `elections`
--

CREATE TABLE `elections` (
  `id` int(11) NOT NULL,
  `club_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `start_datetime` datetime NOT NULL,
  `end_datetime` datetime NOT NULL,
  `is_active` tinyint(1) DEFAULT 0,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `elections`
--

INSERT INTO `elections` (`id`, `club_id`, `title`, `description`, `start_datetime`, `end_datetime`, `is_active`, `created_by`, `created_at`) VALUES
(18, 0, 'IEEE Student Branch Presidential 2025', 'Choose you president, among you.', '2025-10-07 11:26:00', '2025-10-08 19:26:00', 1, 4, '2025-10-07 07:57:30'),
(19, 0, 'Rotaract Club Presidential 2025', 'Choose the president for the rotaracts !', '2025-10-08 14:27:00', '2025-10-09 19:27:00', 0, 4, '2025-10-07 07:58:36'),
(20, 0, ';ls\'fsf', 'sd\';f\'lsf', '2025-10-07 16:24:00', '2025-10-07 21:24:00', 0, 4, '2025-10-07 09:54:54');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `university_email` varchar(255) NOT NULL,
  `role` enum('admin','voter') NOT NULL,
  `password` varchar(255) NOT NULL,
  `club_id` int(11) DEFAULT NULL,
  `faculty` varchar(150) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `university_email`, `role`, `password`, `club_id`, `faculty`, `created_at`) VALUES
(2, 'Voter One', 'voter1@university.edu', 'voter', '$2y$10$Dq4fF8jvZ1k6T7gJ9R6F.uY3kO9Vf1cQ2xZ3H.4N5Yb8QkP1sT1uK', NULL, 'Engineering', '2025-09-13 08:35:03'),
(3, 'Voter Two', 'voter2@university.edu', 'voter', '$2y$10$hH8jK3mN9pQ7rT5xV6bC.oL2rN5yUj1W3eS6Qv2P9aZ8F4dL0kM2G', NULL, 'Business', '2025-09-13 08:35:03'),
(4, 'Admin', 'adminvotekdu@kdu.ac.lk', 'admin', '$2y$10$OX.OC33BdTngEwHSYaRNreNyZ4icDlBJdnKusrWXOxvdYYUhfExc.', NULL, NULL, '2025-09-13 09:23:06'),
(5, 'Heshani Dissanayake', 'hehsani@university.edu.lk', 'voter', '$2y$10$RlFy8jhhrs814PwursVldOTG0/sQNhlfjbEMlh2buOqlVGFpg09Tu', NULL, 'Computing', '2025-09-13 09:27:14'),
(7, 'jon smith', 'eaahashara01@kdu.ac.lk', 'voter', '$2y$10$z44VRqHVcQins4fLle.vZeRzU6q1suq8EmjMQyEyH1HxaCuavrdEi', NULL, 'Engineering', '2025-09-14 13:48:23'),
(8, 'Dnuki nawodya', 'danuki@kdu.ac.lk', 'voter', '$2y$10$INQmsjmZjk9/7IsPpN2.qu/Wtv0gDwJ4NTUI5RfPnZKswr2cQMqpm', NULL, 'Law', '2025-09-17 15:06:38'),
(9, 'Barel Yomal', 'yomal@kdu.ac.lk', 'voter', '$2y$10$4EXs.YlS747zSFHanLZ2s.ax9BbXAc3TrIcqo3Z0j16m5EB0DUdHG', NULL, NULL, '2025-09-22 04:18:00'),
(11, 'Sithum Heshan', 'Sithum@kdu.ac.lk', 'voter', '$2y$10$5ZJ0OoMb4TTi0Q9WMRqi6.89d2VBtbjLEAV3TO2Hogy4iW5hZbnFa', NULL, NULL, '2025-09-22 09:49:02'),
(14, 'Rohit Prabakaran', 'rohit@kdu.ac.lk', 'voter', '$2y$10$yOc9NZ80mGOOsZgm9RDrNuXMCXEW7bwSaloOhTZR0i1igou/5/dZ6', NULL, NULL, '2025-09-29 05:41:57'),
(15, 'isuri', 'isuri@kdu.ac.lk', 'voter', '$2y$10$foClQmwofKi77uj51xxyQet5YfbXZeLAtdQI13/Ck.dsg.UjhCBW.', NULL, NULL, '2025-09-29 08:20:51'),
(16, 'Avishka Dilshan', '42-coe-0019@kdu.ac.lk', 'voter', '$2y$10$y1IGnXULh3OzZWF4w13p../7sVvaNcztOY7tmReOMgm7prMlYzvPi', NULL, NULL, '2025-10-06 14:59:08'),
(17, 'Avishka Hashara', '42-bcs-0016@kdu.ac.lk', 'voter', '$2y$10$NVP2N.ZW3Ujk5CTpA8UiM.jjGQNTr2u5r7lotMLiLq/uuSKR1zepe', NULL, NULL, '2025-10-07 08:22:03'),
(18, 'Pakaya', 'fk@kdu.ac.lk', 'voter', '$2y$10$jrEq90sog4ODcjdFjv4qD.lSViw5ZHNreYk20/zSWvT3jDbQIXQjO', NULL, 'Computing', '2025-10-07 09:50:56');

-- --------------------------------------------------------

--
-- Table structure for table `votes`
--

CREATE TABLE `votes` (
  `id` int(11) NOT NULL,
  `election_id` int(11) NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `cast_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `votes`
--

INSERT INTO `votes` (`id`, `election_id`, `candidate_id`, `user_id`, `cast_at`) VALUES
(12, 18, 24, 17, '2025-10-07 08:42:55');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `candidates`
--
ALTER TABLE `candidates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `election_id` (`election_id`);

--
-- Indexes for table `clubs`
--
ALTER TABLE `clubs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `elections`
--
ALTER TABLE `elections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `club_id` (`club_id`),
  ADD KEY `start_datetime` (`start_datetime`),
  ADD KEY `end_datetime` (`end_datetime`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `university_email` (`university_email`),
  ADD KEY `club_id` (`club_id`);

--
-- Indexes for table `votes`
--
ALTER TABLE `votes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_vote_per_user` (`election_id`,`user_id`),
  ADD KEY `candidate_id` (`candidate_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `candidates`
--
ALTER TABLE `candidates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `clubs`
--
ALTER TABLE `clubs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `elections`
--
ALTER TABLE `elections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `votes`
--
ALTER TABLE `votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `candidates`
--
ALTER TABLE `candidates`
  ADD CONSTRAINT `candidates_ibfk_1` FOREIGN KEY (`election_id`) REFERENCES `elections` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `elections`
--
ALTER TABLE `elections`
  ADD CONSTRAINT `elections_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`club_id`) REFERENCES `clubs` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `votes`
--
ALTER TABLE `votes`
  ADD CONSTRAINT `votes_ibfk_1` FOREIGN KEY (`election_id`) REFERENCES `elections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `votes_ibfk_2` FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `votes_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
