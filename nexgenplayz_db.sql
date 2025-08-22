-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 22, 2025 at 02:10 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

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
  `achievement_id` int NOT NULL,
  `title` varchar(100) COLLATE dec8_bin NOT NULL,
  `icon_class` varchar(100) COLLATE dec8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=dec8 COLLATE=dec8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `games`
--

CREATE TABLE `games` (
  `game_id` int NOT NULL,
  `title` varchar(100) COLLATE dec8_bin NOT NULL,
  `cover_image` varchar(100) COLLATE dec8_bin NOT NULL,
  `video_preview` varchar(100) COLLATE dec8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=dec8 COLLATE=dec8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `support_messages`
--

CREATE TABLE `support_messages` (
  `message_id` int NOT NULL,
  `name` varchar(100) COLLATE dec8_bin NOT NULL,
  `email` varchar(100) COLLATE dec8_bin NOT NULL,
  `subject` varchar(100) COLLATE dec8_bin NOT NULL,
  `message` text COLLATE dec8_bin NOT NULL,
  `submitted_at` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=dec8 COLLATE=dec8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int NOT NULL,
  `username` varchar(100) COLLATE dec8_bin NOT NULL,
  `email` varchar(100) COLLATE dec8_bin NOT NULL,
  `password_hash` varchar(100) COLLATE dec8_bin NOT NULL,
  `profile_picture` varchar(100) COLLATE dec8_bin NOT NULL,
  `tagline` varchar(100) COLLATE dec8_bin NOT NULL,
  `created_at` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=dec8 COLLATE=dec8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `user_achievements`
--

CREATE TABLE `user_achievements` (
  `unlocked_id` int NOT NULL,
  `user_id` int NOT NULL,
  `achievement_id` int NOT NULL,
  `unlocked_at` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=dec8 COLLATE=dec8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `user_owned_games`
--

CREATE TABLE `user_owned_games` (
  `ownership_id` int NOT NULL,
  `user_id` varchar(100) COLLATE dec8_bin NOT NULL,
  `game_id` varchar(100) COLLATE dec8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=dec8 COLLATE=dec8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `user_stats`
--

CREATE TABLE `user_stats` (
  `stat_id` int NOT NULL,
  `user_id` varchar(100) COLLATE dec8_bin NOT NULL,
  `games_played` int NOT NULL,
  `hours_logged` int NOT NULL,
  `wins` int NOT NULL,
  `losses` int NOT NULL,
  `current_rank` varchar(100) COLLATE dec8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=dec8 COLLATE=dec8_bin;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `achievements`
--
ALTER TABLE `achievements`
  ADD PRIMARY KEY (`achievement_id`);

--
-- Indexes for table `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`game_id`);

--
-- Indexes for table `support_messages`
--
ALTER TABLE `support_messages`
  ADD PRIMARY KEY (`message_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

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
  MODIFY `achievement_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `games`
--
ALTER TABLE `games`
  MODIFY `game_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `support_messages`
--
ALTER TABLE `support_messages`
  MODIFY `message_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_achievements`
--
ALTER TABLE `user_achievements`
  MODIFY `unlocked_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_owned_games`
--
ALTER TABLE `user_owned_games`
  MODIFY `ownership_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_stats`
--
ALTER TABLE `user_stats`
  MODIFY `stat_id` int NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
