-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 07, 2026 at 02:39 PM
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
-- Database: `quiz_platform`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `created_at`) VALUES
(2, 'admin', '21232f297a57a5a743894a0e4a801fc3', '2026-01-15 14:26:13');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `status`, `created_at`) VALUES
(1, 'General Knowledge', 'Test your daily awareness.', 'active', '2026-01-14 16:59:59'),
(2, 'Competitive Exams', 'Exam prep material.', 'active', '2026-01-14 16:59:59'),
(3, 'Programming Basics', 'Coding fundamentals.', 'active', '2026-01-14 16:59:59');

-- --------------------------------------------------------

--
-- Table structure for table `certificates`
--

CREATE TABLE `certificates` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `certificate_code` varchar(50) NOT NULL,
  `issued_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `options`
--

CREATE TABLE `options` (
  `id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `option_text` text DEFAULT NULL,
  `option_image` varchar(255) DEFAULT NULL,
  `is_correct` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `options`
--

INSERT INTO `options` (`id`, `question_id`, `option_text`, `option_image`, `is_correct`, `created_at`) VALUES
(1, 1, 'Berlin', NULL, 0, '2026-01-14 16:59:59'),
(2, 1, 'Madrid', NULL, 0, '2026-01-14 16:59:59'),
(3, 1, 'Paris', NULL, 1, '2026-01-14 16:59:59'),
(4, 1, 'Rome', NULL, 0, '2026-01-14 16:59:59'),
(5, 2, 'Atlantic', NULL, 0, '2026-01-14 16:59:59'),
(6, 2, 'Indian', NULL, 0, '2026-01-14 16:59:59'),
(7, 2, 'Pacific', NULL, 1, '2026-01-14 16:59:59'),
(8, 2, 'Arctic', NULL, 0, '2026-01-14 16:59:59'),
(9, 3, 'Oxygen', NULL, 0, '2026-01-14 16:59:59'),
(10, 3, 'Water', NULL, 1, '2026-01-14 16:59:59'),
(11, 3, 'Hydrogen', NULL, 0, '2026-01-14 16:59:59'),
(12, 3, 'Ice', NULL, 0, '2026-01-14 16:59:59'),
(13, 4, 'Asia', NULL, 0, '2026-01-14 16:59:59'),
(14, 4, 'Europe', NULL, 0, '2026-01-14 16:59:59'),
(15, 4, 'Australia', NULL, 1, '2026-01-14 16:59:59'),
(16, 4, 'Africa', NULL, 0, '2026-01-14 16:59:59'),
(17, 5, '365', NULL, 0, '2026-01-14 16:59:59'),
(18, 5, '366', NULL, 1, '2026-01-14 16:59:59'),
(19, 5, '364', NULL, 0, '2026-01-14 16:59:59'),
(20, 5, '360', NULL, 0, '2026-01-14 16:59:59'),
(21, 6, 'Yuri Gagarin', NULL, 0, '2026-01-14 16:59:59'),
(22, 6, 'Neil Armstrong', NULL, 1, '2026-01-14 16:59:59'),
(23, 6, 'Buzz Aldrin', NULL, 0, '2026-01-14 16:59:59'),
(24, 6, 'Michael Collins', NULL, 0, '2026-01-14 16:59:59'),
(25, 7, 'Yuan', NULL, 0, '2026-01-14 16:59:59'),
(26, 7, 'Won', NULL, 0, '2026-01-14 16:59:59'),
(27, 7, 'Yen', NULL, 1, '2026-01-14 16:59:59'),
(28, 7, 'Dollar', NULL, 0, '2026-01-14 16:59:59'),
(29, 8, 'Sydney', NULL, 0, '2026-01-14 16:59:59'),
(30, 8, 'Melbourne', NULL, 0, '2026-01-14 16:59:59'),
(31, 8, 'Canberra', NULL, 1, '2026-01-14 16:59:59'),
(32, 8, 'Perth', NULL, 0, '2026-01-14 16:59:59'),
(33, 9, 'Dickens', NULL, 0, '2026-01-14 16:59:59'),
(34, 9, 'Shakespeare', NULL, 1, '2026-01-14 16:59:59'),
(35, 9, 'Hemingway', NULL, 0, '2026-01-14 16:59:59'),
(36, 9, 'Austen', NULL, 0, '2026-01-14 16:59:59'),
(37, 10, 'Gold', NULL, 0, '2026-01-14 16:59:59'),
(38, 10, 'Iron', NULL, 0, '2026-01-14 16:59:59'),
(39, 10, 'Diamond', NULL, 1, '2026-01-14 16:59:59'),
(40, 10, 'Platinum', NULL, 0, '2026-01-14 16:59:59'),
(41, 11, 'Nehru', NULL, 0, '2026-01-14 16:59:59'),
(42, 11, 'Mountbatten', NULL, 1, '2026-01-14 16:59:59'),
(43, 11, 'Rajaji', NULL, 0, '2026-01-14 16:59:59'),
(44, 11, 'Prasad', NULL, 0, '2026-01-14 16:59:59'),
(45, 12, '133', NULL, 1, '2026-01-14 16:59:59'),
(46, 12, '1330', NULL, 0, '2026-01-14 16:59:59'),
(47, 12, '13', NULL, 0, '2026-01-14 16:59:59'),
(48, 12, '330', NULL, 0, '2026-01-14 16:59:59'),
(49, 13, 'Part I', NULL, 0, '2026-01-14 16:59:59'),
(50, 13, 'Part II', NULL, 0, '2026-01-14 16:59:59'),
(51, 13, 'Part III', NULL, 1, '2026-01-14 16:59:59'),
(52, 13, 'Part IV', NULL, 0, '2026-01-14 16:59:59'),
(53, 14, 'Verghese Kurien', NULL, 0, '2026-01-14 16:59:59'),
(54, 14, 'M.S. Swaminathan', NULL, 1, '2026-01-14 16:59:59'),
(55, 14, 'Norman Borlaug', NULL, 0, '2026-01-14 16:59:59'),
(56, 14, 'Amartya Sen', NULL, 0, '2026-01-14 16:59:59'),
(57, 15, 'Delhi', NULL, 0, '2026-01-14 16:59:59'),
(58, 15, 'Chennai', NULL, 0, '2026-01-14 16:59:59'),
(59, 15, 'Kolkata', NULL, 0, '2026-01-14 16:59:59'),
(60, 15, 'Mumbai', NULL, 1, '2026-01-14 16:59:59'),
(61, 16, 'Hyper Text Markup Language', NULL, 1, '2026-01-14 16:59:59'),
(62, 16, 'High Text Machine Language', NULL, 0, '2026-01-14 16:59:59'),
(63, 16, 'Hyper Tool Multi Language', NULL, 0, '2026-01-14 16:59:59'),
(64, 16, 'None', NULL, 0, '2026-01-14 16:59:59'),
(65, 17, '<h6>', NULL, 0, '2026-01-14 16:59:59'),
(66, 17, '<head>', NULL, 0, '2026-01-14 16:59:59'),
(67, 17, '<h1>', NULL, 1, '2026-01-14 16:59:59'),
(68, 17, '<header>', NULL, 0, '2026-01-14 16:59:59'),
(69, 18, 'Creative Style Sheets', NULL, 0, '2026-01-14 16:59:59'),
(70, 18, 'Cascading Style Sheets', NULL, 1, '2026-01-14 16:59:59'),
(71, 18, 'Colorful Style Sheets', NULL, 0, '2026-01-14 16:59:59'),
(72, 18, 'Computer Style Sheets', NULL, 0, '2026-01-14 16:59:59'),
(73, 19, 'text-color', NULL, 0, '2026-01-14 16:59:59'),
(74, 19, 'font-color', NULL, 0, '2026-01-14 16:59:59'),
(75, 19, 'color', NULL, 1, '2026-01-14 16:59:59'),
(76, 19, 'fg-color', NULL, 0, '2026-01-14 16:59:59'),
(77, 20, '<b>', NULL, 1, '2026-01-14 16:59:59'),
(78, 20, '<bold>', NULL, 0, '2026-01-14 16:59:59'),
(79, 20, '<bb>', NULL, 0, '2026-01-14 16:59:59'),
(80, 20, '<dark>', NULL, 0, '2026-01-14 16:59:59'),
(89, 23, 'camstrup', 'kampstrup.png', 1, '2026-01-15 14:36:39'),
(90, 23, 'camstrup', 'baapi.png', 0, '2026-01-15 14:36:39'),
(91, 23, 'camstrup', 'kamstrup.png', 0, '2026-01-15 14:36:39'),
(92, 23, 'camstrup', 'kamstrup.png', 0, '2026-01-15 14:36:39'),
(93, 24, 'camstrup', 'kampstrup.png', 1, '2026-01-15 14:43:05'),
(94, 24, 'camstrup', 'baapi.png', 0, '2026-01-15 14:43:05'),
(95, 24, 'camstrup', 'kamstrup.png', 0, '2026-01-15 14:43:05'),
(96, 24, 'camstrup', 'kamstrup.png', 0, '2026-01-15 14:43:05'),
(97, 25, 'camstrup', 'kampstrup.png', 1, '2026-01-15 14:44:04'),
(98, 25, 'camstrup', 'baapi.png', 0, '2026-01-15 14:44:04'),
(99, 25, 'camstrup', 'kamstrup.png', 0, '2026-01-15 14:44:04'),
(100, 25, 'camstrup', 'kamstrup.png', 0, '2026-01-15 14:44:04'),
(101, 26, 'camstrup', 'kampstrup.png', 1, '2026-01-15 14:44:53'),
(102, 26, 'camstrup', 'baapi.png', 0, '2026-01-15 14:44:53'),
(103, 26, 'camstrup', 'kamstrup.png', 0, '2026-01-15 14:44:53'),
(104, 26, 'camstrup', 'kamstrup.png', 0, '2026-01-15 14:44:53');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `quiz_id` int(11) DEFAULT NULL,
  `transaction_id` varchar(100) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(10) DEFAULT 'USD',
  `payment_status` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `user_id`, `quiz_id`, `transaction_id`, `amount`, `currency`, `payment_status`, `created_at`) VALUES
(1, 1, 3, '8DF93189ST1601007', 15.00, 'USD', 'COMPLETED', '2026-01-14 17:51:21'),
(2, 1, 2, '6G863017NU8223010', 10.00, 'USD', 'COMPLETED', '2026-01-19 17:49:43');

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `question_text` text DEFAULT NULL,
  `question_image` varchar(255) DEFAULT NULL,
  `question_type` enum('text','image','both') DEFAULT 'text',
  `answer_type` enum('text','image','both') DEFAULT 'text',
  `marks` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_image`, `question_type`, `answer_type`, `marks`, `created_at`) VALUES
(1, 1, 'What is the capital of France?', NULL, 'text', 'text', 1, '2026-01-14 16:59:59'),
(2, 1, 'Largest ocean in the world?', NULL, 'text', 'text', 1, '2026-01-14 16:59:59'),
(3, 1, 'H2O is the chemical formula for?', NULL, 'text', 'text', 1, '2026-01-14 16:59:59'),
(4, 1, 'Which is the smallest continent?', NULL, 'text', 'text', 1, '2026-01-14 16:59:59'),
(5, 1, 'Number of days in a leap year?', NULL, 'text', 'text', 1, '2026-01-14 16:59:59'),
(6, 2, 'First man to step on the Moon?', NULL, 'text', 'text', 1, '2026-01-14 16:59:59'),
(7, 2, 'Currency of Japan?', NULL, 'text', 'text', 1, '2026-01-14 16:59:59'),
(8, 2, 'Capital of Australia?', NULL, 'text', 'text', 1, '2026-01-14 16:59:59'),
(9, 2, 'Who wrote \"Romeo and Juliet\"?', NULL, 'text', 'text', 1, '2026-01-14 16:59:59'),
(10, 2, 'Hardest natural substance?', NULL, 'text', 'text', 1, '2026-01-14 16:59:59'),
(11, 3, 'First Governor General of Independent India?', NULL, 'text', 'text', 1, '2026-01-14 16:59:59'),
(12, 3, 'Thirukkural has how many chapters?', NULL, 'text', 'text', 1, '2026-01-14 16:59:59'),
(13, 3, 'Fundamental Rights are in which part of Constitution?', NULL, 'text', 'text', 1, '2026-01-14 16:59:59'),
(14, 3, 'Father of Green Revolution in India?', NULL, 'text', 'text', 1, '2026-01-14 16:59:59'),
(15, 3, 'RBI Headquarters is in?', NULL, 'text', 'text', 1, '2026-01-14 16:59:59'),
(16, 4, 'HTML stands for?', NULL, 'text', 'text', 1, '2026-01-14 16:59:59'),
(17, 4, 'Which tag is used for largest heading?', NULL, 'text', 'text', 1, '2026-01-14 16:59:59'),
(18, 4, 'CSS stands for?', NULL, 'text', 'text', 1, '2026-01-14 16:59:59'),
(19, 4, 'Which property changes text color?', NULL, 'text', 'text', 1, '2026-01-14 16:59:59'),
(20, 4, 'To make text bold, we use?', NULL, 'text', 'text', 1, '2026-01-14 16:59:59'),
(23, 4, 'what is the kamstrup', 'kamstrup.png', 'image', 'text', 1, '2026-01-15 14:36:39'),
(24, 4, 'what is the kamstrup', 'kamstrup.png', 'image', NULL, 1, '2026-01-15 14:43:05'),
(25, 4, 'what is the kamstrup', 'kamstrup.png', 'image', NULL, 1, '2026-01-15 14:44:04'),
(26, 4, 'what is the kamstrup', 'kamstrup.png', 'image', 'text', 1, '2026-01-15 14:44:53');

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT 0.00,
  `time_limit` int(11) NOT NULL COMMENT 'In minutes',
  `total_marks` int(11) NOT NULL,
  `pass_percentage` int(11) NOT NULL DEFAULT 50,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `max_attempts` int(11) DEFAULT 0 COMMENT '0 for unlimited',
  `enable_certificate` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quizzes`
--

INSERT INTO `quizzes` (`id`, `category_id`, `title`, `description`, `price`, `time_limit`, `total_marks`, `pass_percentage`, `status`, `created_at`, `max_attempts`, `enable_certificate`) VALUES
(1, 1, 'GK Level 1', 'Beginner general knowledge.', 5.00, 10, 5, 50, 'active', '2026-01-14 16:59:59', 0, 0),
(2, 1, 'GK Level 2', 'Intermediate questions.', 10.00, 15, 5, 50, 'active', '2026-01-14 16:59:59', 0, 0),
(3, 2, 'TNPSC Model Test', 'Group 4 model questions.', 15.00, 20, 5, 50, 'active', '2026-01-14 16:59:59', 0, 0),
(4, 3, 'HTML & CSS Quiz', 'Web design basics.', 0.00, 15, 5, 50, 'active', '2026-01-14 16:59:59', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `quiz_attempts`
--

CREATE TABLE `quiz_attempts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime DEFAULT NULL,
  `score` decimal(10,2) DEFAULT 0.00,
  `total_questions` int(11) DEFAULT 0,
  `status` enum('ongoing','completed') DEFAULT 'ongoing',
  `attempt_number` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quiz_attempts`
--

INSERT INTO `quiz_attempts` (`id`, `user_id`, `quiz_id`, `start_time`, `end_time`, `score`, `total_questions`, `status`, `attempt_number`) VALUES
(1, 1, 3, '2026-01-14 18:51:28', '2026-01-14 23:21:40', 20.00, 5, 'completed', 1),
(2, 1, 4, '2026-01-14 19:06:11', '2026-01-14 23:36:19', 20.00, 5, 'completed', 1),
(3, 1, 4, '2026-01-14 19:06:22', '2026-01-15 19:59:33', 0.00, 6, 'completed', 2),
(4, 1, 3, '2026-01-15 15:29:22', '2026-01-15 20:26:09', 0.00, 5, 'completed', 2),
(5, 1, 4, '2026-01-15 15:29:37', '2026-01-15 20:00:04', 33.33, 6, 'completed', 3),
(6, 1, 4, '2026-01-15 15:31:49', '2026-01-15 20:02:11', 100.00, 6, 'completed', 4),
(7, 1, 4, '2026-01-15 15:36:52', '2026-01-15 20:26:05', 0.00, 9, 'completed', 5),
(8, 1, 4, '2026-01-15 15:56:11', '2026-01-16 00:13:53', 0.00, 9, 'completed', 6),
(9, 1, 3, '2026-01-15 16:00:15', '2026-01-18 21:42:27', 0.00, 5, 'completed', 3),
(10, 1, 4, '2026-01-15 19:43:55', '2026-01-16 00:14:01', 0.00, 9, 'completed', 7),
(11, 1, 4, '2026-01-18 16:30:09', '2026-01-18 21:00:55', 33.33, 9, 'completed', 8),
(12, 1, 3, '2026-01-18 17:12:34', '2026-01-18 22:28:17', 0.00, 5, 'completed', 4),
(13, 1, 4, '2026-01-18 17:12:55', '2026-01-18 21:42:59', 0.00, 9, 'completed', 9),
(14, 1, 4, '2026-01-18 17:58:21', '2026-01-18 22:28:34', 11.11, 9, 'completed', 10),
(15, 1, 4, '2026-01-19 18:49:51', '2026-01-19 23:20:37', 44.44, 9, 'completed', 11),
(16, 1, 4, '2026-01-19 18:51:14', '2026-01-19 23:21:45', 22.22, 9, 'completed', 12),
(17, 1, 3, '2026-01-19 19:03:21', NULL, 0.00, 0, 'ongoing', 5),
(18, 1, 2, '2026-02-07 14:36:56', '2026-02-07 19:07:01', 0.00, 5, 'completed', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `created_at`) VALUES
(1, 'sura techie', 'cellicella0@gmail.com', '$2y$10$SnXZ1K3wsdqfbzFhua/Yg.ZxBN53XyGFduDnPn8kjBaZBgxVjtdCu', '2026-01-14 16:54:40');

-- --------------------------------------------------------

--
-- Table structure for table `user_quiz_access`
--

CREATE TABLE `user_quiz_access` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `access_granted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_quiz_access`
--

INSERT INTO `user_quiz_access` (`id`, `user_id`, `quiz_id`, `access_granted_at`) VALUES
(1, 1, 3, '2026-01-14 17:51:21'),
(2, 1, 2, '2026-01-19 17:49:43');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `certificates`
--
ALTER TABLE `certificates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `certificate_code` (`certificate_code`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- Indexes for table `options`
--
ALTER TABLE `options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- Indexes for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_quiz_access`
--
ALTER TABLE `user_quiz_access`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `certificates`
--
ALTER TABLE `certificates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `options`
--
ALTER TABLE `options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_quiz_access`
--
ALTER TABLE `user_quiz_access`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `certificates`
--
ALTER TABLE `certificates`
  ADD CONSTRAINT `certificates_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `certificates_ibfk_2` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `options`
--
ALTER TABLE `options`
  ADD CONSTRAINT `options_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD CONSTRAINT `quizzes_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  ADD CONSTRAINT `quiz_attempts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quiz_attempts_ibfk_2` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_quiz_access`
--
ALTER TABLE `user_quiz_access`
  ADD CONSTRAINT `user_quiz_access_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_quiz_access_ibfk_2` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
