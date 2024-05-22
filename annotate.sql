-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 22, 2024 at 04:45 PM
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
-- Database: `annotate`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(1, 'admin', 'password');

-- --------------------------------------------------------

--
-- Table structure for table `annotations`
--

CREATE TABLE `annotations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `annotations`
--

INSERT INTO `annotations` (`id`, `user_id`, `image_path`, `created_at`) VALUES
(310, 1, 'dataset/annotated/train/images/clip2--100-_jpg.rf.157bcc9cbb66675ad450232bb92b1027.jpg', '2024-05-22 10:41:52'),
(313, 1, 'dataset/annotated/train/images/clip2--102-_jpg.rf.921f1bcc36e661db582ab6c24f2fe219.jpg', '2024-05-22 10:42:19'),
(316, 1, 'dataset/annotated/train/images/clip2--103-_jpg.rf.884bf0d6805146d8995df53db4f584b5.jpg', '2024-05-22 10:42:28'),
(320, 1, 'dataset/annotated/train/images/clip2--104-_jpg.rf.10d35b15872b3b94c63a1dd8941c2ce6.jpg', '2024-05-22 10:42:35'),
(324, 1, 'dataset/annotated/train/images/clip2--108-_jpg.rf.34b838a7c4710542f6bd0eaff2f4c7e4.jpg', '2024-05-22 10:42:51'),
(326, 1, 'dataset/annotated/train/images/clip2--109-_jpg.rf.965ad7c361b619a2fd8d73ba80142511.jpg', '2024-05-22 10:42:53'),
(328, 1, 'dataset/annotated/train/images/clip2--111-_jpg.rf.9dee0f8cede63071f7dd984d172bcbf0.jpg', '2024-05-22 10:42:55'),
(330, 1, 'dataset/annotated/train/images/clip2--112-_jpg.rf.a4efd95abe76124f6788d74ac80aeb50.jpg', '2024-05-22 10:42:57'),
(332, 1, 'dataset/annotated/train/images/clip2--113-_jpg.rf.42d0d7a76d46328941f9df61622f0b81.jpg', '2024-05-22 10:43:01'),
(334, 1, 'dataset/annotated/train/images/clip2--114-_jpg.rf.b4676b2cf6009e0a6942061576629cfd.jpg', '2024-05-22 10:43:03'),
(336, 1, 'dataset/annotated/train/images/clip2--116-_jpg.rf.4bfed73f932a79f8ac4a5175d3fda3ae.jpg', '2024-05-22 10:43:07'),
(338, 1, 'dataset/annotated/train/images/clip2--117-_jpg.rf.c3c98078fc9e425edfb1ed60e4129eb9.jpg', '2024-05-22 10:43:11'),
(342, 1, 'dataset/annotated/train/images/clip2--118-_jpg.rf.19ca7124a6e865baa24a61fdd92feb48.jpg', '2024-05-22 10:43:19'),
(346, 1, 'dataset/annotated/train/images/clip2--120-_jpg.rf.479b2ad26b162dc3a5a2d19d5a77d8ec.jpg', '2024-05-22 10:43:29'),
(348, 1, 'dataset/annotated/train/images/clip2--121-_jpg.rf.bce57faecbd498a33fb1d3cb077d4529.jpg', '2024-05-22 10:45:34'),
(350, 1, 'dataset/annotated/train/images/clip2--126-_jpg.rf.b106ca714cbe433c5b3e91b2c0fb8ead.jpg', '2024-05-22 10:45:39'),
(356, 1, 'dataset/annotated/train/images/clip2--127-_jpg.rf.c48ac7de00d338b2f15a604757991c82.jpg', '2024-05-22 10:45:56'),
(359, 1, 'dataset/annotated/train/images/clip2--129-_jpg.rf.7844ea7ab5343121a88690c34c62e891.jpg', '2024-05-22 10:46:32'),
(360, 1, 'dataset/annotated/train/images/clip2--13-_jpg.rf.6648a5fbfb3d16bb5462f282beeaf0c4.jpg', '2024-05-22 10:48:24'),
(362, 1, 'dataset/annotated/train/images/clip2--130-_jpg.rf.07e11764441d56e8455427c0e861194b.jpg', '2024-05-22 10:48:28'),
(365, 1, 'dataset/annotated/train/images/clip2--132-_jpg.rf.9bf01a4cf829aad347232d9fcc2964ed.jpg', '2024-05-22 10:49:49'),
(366, 1, 'dataset/annotated/train/images/clip2--133-_jpg.rf.9a8e451a2317695a695c9497accfdf36.jpg', '2024-05-22 10:50:47'),
(368, 1, 'dataset/annotated/train/images/clip2--134-_jpg.rf.c235546a9ed3a5812579c64569dc6ea9.jpg', '2024-05-22 10:51:28'),
(369, 1, 'dataset/annotated/train/images/clip2--136-_jpg.rf.986af752b6c8b3c5b339e3c2afc213f4.jpg', '2024-05-22 10:51:37'),
(370, 1, 'dataset/annotated/train/images/clip2--137-_jpg.rf.d82b8c1418f296c732a6ec7cc20ce817.jpg', '2024-05-22 10:52:02'),
(371, 1, 'dataset/annotated/train/images/clip2--138-_jpg.rf.98ae71767221da5367de8ca4fb0bd826.jpg', '2024-05-22 10:54:27'),
(372, 1, 'dataset/annotated/train/images/clip2--139-_jpg.rf.9da1b5c97b5d1592f9facf79802a1ce8.jpg', '2024-05-22 10:54:46');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `credit` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password`, `credit`) VALUES
(1, 'Akibur', 'Rahman', 'me.akiburrahman@gmail.com', '$2y$10$kxH/YZOhlHJZv1F3rxGSFOebRthSCF3p2BCFMt9fu7a3uhEumk8y2', 75),
(2, 'Laptop', 'Mia', 'laptop@gmail.com', '$2y$10$LoxYGazhT/ffMkJgOBGRgeu4HDKFqwFYj16zXDZW2slBh3QB8bkOy', 0),
(3, 'rase', 'asd', 'asd@gmail.com', '$2y$10$LlYJjtxlzICnHRofCitEhuh2Q6TF8bDvMe9RM9mzOs6P404VdBVOS', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `annotations`
--
ALTER TABLE `annotations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`image_path`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `annotations`
--
ALTER TABLE `annotations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=385;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `annotations`
--
ALTER TABLE `annotations`
  ADD CONSTRAINT `annotations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
