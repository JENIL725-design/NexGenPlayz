-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 12, 2025 at 05:32 PM
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
-- Database: `nexgenplayz_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `achievements`
--

CREATE TABLE `achievements` (
  `achievement_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `icon_class` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=dec8 COLLATE=dec8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `username`, `email`, `password_hash`) VALUES
(1, 'JENIL', 'JENIL@gmail.com', '102030'),
(2, 'SOME', 'SOME@gmail.com', '10');

-- --------------------------------------------------------

--
-- Table structure for table `games`
--

CREATE TABLE `games` (
  `game_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `cover_image` varchar(100) NOT NULL,
  `video_preview` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=dec8 COLLATE=dec8_bin;

--
-- Dumping data for table `games`
--

INSERT INTO `games` (`game_id`, `title`, `cover_image`, `video_preview`, `price`) VALUES
(9, 'FORZA 5', 'img/Forza5.png', 'video/FORZA 480.mp4', 79.99),
(10, 'BLACK MYTH', 'img/Black.png', 'video/BLACK MYTH 480.mp4', 89.99),
(11, 'BLACK OPS 6', 'img/Blackops6.png', 'video/Call of Duty Black Ops 6 480.mp4', 59.99),
(12, 'CYBERPUNK 2077', 'img/2077.jpg', 'video/Cyberpunk 2077 480.mp4', 79.99),
(13, 'Assassin\'s Creed Brotherhood', 'img/Brother.png', 'video/Assassin\'s Creed Brotherhood 480.mp4', 69.99),
(14, 'Apex Legends ', 'img/apex.png', 'video/APEXLEGEND 480.mp4', 49.99),
(15, 'Battlefield 2042', 'img/2042.png', 'video/Battlefiled 2042.mp4', 52.99),
(16, 'GTA 6', 'img/gta 6.png', 'video/GTA VI 480.mp4', 99.99),
(17, 'ILL', 'img/ill.png', 'video/ILL 480.mp4', 47.99),
(18, 'JUST CAUSE 4', 'img/JustCause4.png', 'video/JUST CAUSE 4 480.mp4', 38.99),
(21, 'SEKIRO ', 'img/Sekiro.png', 'video/Sekiro 480.mp4', 59.99);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `game_id` int(11) NOT NULL,
  `card_number` varchar(16) NOT NULL,
  `card_holder` varchar(255) NOT NULL,
  `expiry` varchar(5) NOT NULL,
  `cvv` varchar(4) NOT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `user_id`, `game_id`, `card_number`, `card_holder`, `expiry`, `cvv`, `payment_date`) VALUES
(6, 6, 9, '5798321475863259', 'USER GAMER', '2345', '7846', '2025-09-25 14:20:29'),
(7, 6, 16, '3574869158723548', 'PRO GAMER', '8753', '247', '2025-09-25 15:01:28'),
(8, 6, 10, '8657485396857134', 'Doe Jhon', '5246', '353', '2025-10-01 13:29:34'),
(10, 6, 14, '2647564174123762', 'NEW ERA', '7545', '786', '2025-10-01 16:07:01'),
(11, 13, 16, '5483483218748654', 'YOUR GAMING', '7824', '786', '2025-10-12 10:45:30'),
(12, 6, 13, '9874164684231378', 'Who is this ?', '7549', '485', '2025-10-12 14:27:38');

-- --------------------------------------------------------

--
-- Table structure for table `support_messages`
--

CREATE TABLE `support_messages` (
  `message_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=dec8 COLLATE=dec8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(20) NOT NULL,
  `profile_picture` varchar(100) NOT NULL,
  `tagline` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=dec8 COLLATE=dec8_bin;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password_hash`, `profile_picture`, `tagline`, `created_at`) VALUES
(6, 'GAMING PRO', 'jenil@gmail.com', 'JENIL', 'uploads/profile_photos/profile_68d559657edcb.png', '', '2025-10-12 10:36:28'),
(13, 'something', 'something@gmail.com', '123', 'uploads/profile_photos/profile_68eb86658a6f6.png', '', '2025-10-12 10:43:49');

-- --------------------------------------------------------

--
-- Table structure for table `user_achievements`
--

CREATE TABLE `user_achievements` (
  `unlocked_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `achievement_id` int(11) NOT NULL,
  `unlocked_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=dec8 COLLATE=dec8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `user_owned_games`
--

CREATE TABLE `user_owned_games` (
  `ownership_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `game_id` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=dec8 COLLATE=dec8_bin;

--
-- Dumping data for table `user_owned_games`
--

INSERT INTO `user_owned_games` (`ownership_id`, `user_id`, `game_id`) VALUES
(26, 6, '9'),
(27, 6, '16'),
(28, 6, '10'),
(30, 6, '14'),
(31, 13, '16'),
(32, 6, '13');

-- --------------------------------------------------------

--
-- Table structure for table `user_stats`
--

CREATE TABLE `user_stats` (
  `stat_id` int(11) NOT NULL,
  `user_id` varchar(100) NOT NULL,
  `games_played` int(11) NOT NULL,
  `hours_logged` int(11) NOT NULL,
  `wins` int(11) NOT NULL,
  `losses` int(11) NOT NULL,
  `current_rank` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=dec8 COLLATE=dec8_bin;

--
-- Dumping data for table `user_stats`
--

INSERT INTO `user_stats` (`stat_id`, `user_id`, `games_played`, `hours_logged`, `wins`, `losses`, `current_rank`) VALUES
(1, '6', 0, 1, 0, 0, ''),
(2, '6', 0, 0, 0, 0, ''),
(3, '6', 0, 0, 0, 0, ''),
(4, '6', 0, 0, 0, 0, ''),
(5, '6', 0, 0, 0, 0, ''),
(6, '6', 0, 0, 0, 0, ''),
(7, '6', 0, 0, 0, 0, ''),
(8, '6', 0, 0, 0, 0, ''),
(9, '6', 0, 0, 0, 0, ''),
(10, '6', 0, 0, 0, 0, ''),
(11, '6', 0, 0, 0, 0, ''),
(12, '6', 0, 0, 0, 0, ''),
(13, '6', 0, 0, 0, 0, ''),
(14, '13', 0, 0, 0, 0, ''),
(15, '6', 0, 0, 0, 0, ''),
(16, '6', 0, 0, 0, 0, ''),
(17, '6', 0, 0, 0, 0, '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `achievements`
--
ALTER TABLE `achievements`
  ADD PRIMARY KEY (`achievement_id`);

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`game_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `game_id` (`game_id`);

--
-- Indexes for table `support_messages`
--
ALTER TABLE `support_messages`
  ADD PRIMARY KEY (`message_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_achievements`
--
ALTER TABLE `user_achievements`
  ADD PRIMARY KEY (`unlocked_id`);

--
-- Indexes for table `user_owned_games`
--
ALTER TABLE `user_owned_games`
  ADD PRIMARY KEY (`ownership_id`);

--
-- Indexes for table `user_stats`
--
ALTER TABLE `user_stats`
  ADD PRIMARY KEY (`stat_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `achievements`
--
ALTER TABLE `achievements`
  MODIFY `achievement_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `games`
--
ALTER TABLE `games`
  MODIFY `game_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `support_messages`
--
ALTER TABLE `support_messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `user_achievements`
--
ALTER TABLE `user_achievements`
  MODIFY `unlocked_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_owned_games`
--
ALTER TABLE `user_owned_games`
  MODIFY `ownership_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `user_stats`
--
ALTER TABLE `user_stats`
  MODIFY `stat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`game_id`) REFERENCES `games` (`game_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
