-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : sam. 17 mai 2025 à 12:38
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `clubsync1`
--

-- --------------------------------------------------------

--
-- Structure de la table `club`
--

CREATE TABLE `club` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `members` int(11) NOT NULL,
  `president` varchar(255) NOT NULL,
  `foundation` date DEFAULT NULL,
  `status` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` longtext NOT NULL,
  `join_request` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '(DC2Type:json)' CHECK (json_valid(`join_request`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `club`
--

INSERT INTO `club` (`id`, `name`, `members`, `president`, `foundation`, `status`, `image`, `description`, `join_request`) VALUES
(5, 'enactus', 3, 'talel', '2012-02-09', 'Active', '680e4f18af606.png', 'skffkfj', '[]'),
(6, 'IEEE', 3, 'talel', '2025-12-03', 'Active', '680eab803513f0.72375349.png', 'aaaaaaaaa', '[{\"user_id\":6,\"username\":\"iyed hajri\",\"email\":\"iyedhajri@gmail.com\",\"department\":\"IT\",\"class\":\"L2DSI3\",\"reason\":\"i want to join this club to make more friends and look for the support for my carrer\",\"status\":\"pending\",\"created_at\":\"2025-05-14 22:13:33\",\"updated_at\":null},{\"user_id\":5,\"username\":\"ahmed mekki\",\"email\":\"ahmedmekki@gmail.com\",\"department\":\"IT\",\"class\":\"L2DSI3\",\"reason\":\"aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa\",\"status\":\"pending\",\"created_at\":\"2025-05-15 20:10:33\",\"updated_at\":null}]'),
(7, 'robotic', 12, 'dhib', '2025-05-14', 'Active', '68278855796fd0.59455041.png', 'robotic', '[{\"user_id\":4,\"username\":\"exemple3\",\"email\":\"exemple3@gmail.com\",\"department\":null,\"class\":\"L2DSI3\",\"reason\":\"aaahhhh\",\"status\":\"pending\",\"created_at\":\"2025-05-14 00:13:31\",\"updated_at\":null}]'),
(8, 'Media Club', 15, 'Amira L.', '2018-10-12', 'Active', '68278abd718b68.26169059.jpg', 'A club for aspiring filmmakers and photographers.', '[]'),
(9, 'Green Earth', 20, 'Yassine M.', '2016-04-22', 'Active', '68278ad3678f44.37099478.jpg', 'Environmental awareness and sustainability projects.', '[]'),
(10, 'Music Vibes', 10, 'Sara B.', '2019-03-05', 'Active', '68278b4c9e6b05.99117947.jpg', 'For students passionate about music and performance.', '[]'),
(11, 'Health & Wellness', 18, 'Omar R.', '2020-09-17', 'Active', '68278da9044e08.41051244.png', 'Promoting healthy living and mental well-being.', '[]'),
(12, 'Code Titans', 25, 'Ines K.', '2021-01-10', 'Active', '68278de64254d7.72325598.jpg', 'Programming, hackathons, and software development.', '[]');

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20250409114813', '2025-04-09 13:48:30', 172),
('DoctrineMigrations\\Version20250424102451', '2025-04-24 12:25:09', 67),
('DoctrineMigrations\\Version20250426112247', '2025-04-26 13:44:04', 162),
('DoctrineMigrations\\Version20250427094200', '2025-04-27 11:42:11', 35),
('DoctrineMigrations\\Version20250514093517', '2025-05-14 11:43:00', 260);

-- --------------------------------------------------------

--
-- Structure de la table `event`
--

CREATE TABLE `event` (
  `id` int(11) NOT NULL,
  `club_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `location` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `subscriber` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '(DC2Type:json)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `event`
--

INSERT INTO `event` (`id`, `club_id`, `name`, `description`, `start_date`, `end_date`, `location`, `status`, `image`, `start_time`, `end_time`, `subscriber`) VALUES
(3, 5, 'Hackaton', 'skdsjdmlksmdlj', '2025-03-15', '2025-03-15', 'iset', 'Upcoming', '680e64c5cf2b23.95898800.png', '10:15:00', '12:00:00', '[{\"user_id\":8,\"username\":\"exemple2\",\"email\":\"exemple2@gmail.com\",\"isMemberOfClub\":false},{\"user_id\":9,\"username\":\"iyed hajri\",\"email\":\"iyedhajri@gmail.com\",\"isMemberOfClub\":true}]'),
(4, 5, 'Entrepreneurship Bootcamp', 'Learn how to launch social enterprises.', '2025-06-10', '2025-06-10', 'Innovation Hall', 'Upcoming', '6827b2842ae296.56256466.png', '09:00:00', '12:00:00', '[]'),
(5, 5, 'Social Impact Day', 'Showcasing impactful student projects.', '2025-07-15', '2025-07-15', 'Main Auditorium', 'Upcoming', '6827b3811de5a4.46392682.png', '10:00:00', '13:00:00', '[{\"user_id\":null,\"username\":\"aaaa\",\"email\":\"aaaa@gmail.com\",\"isMemberOfClub\":false}]'),
(6, 5, 'Pitch & Win', 'Pitch your business idea for funding.', '2025-08-01', '2025-08-01', 'Startup Hub', 'Upcoming', '6827bcd202ab06.42296760.jpg', '11:00:00', '14:00:00', '[]'),
(7, 6, 'Tech Conference', 'Annual IEEE tech meetup with speakers.', '2025-09-05', '2025-09-05', 'Engineering Block', 'Upcoming', 'https://via.placeholder.com/150?text=IEEEConf', '09:30:00', '15:00:00', ''),
(8, 6, 'AI Workshop', 'Hands-on AI workshop for beginners.', '2025-10-12', '2025-10-12', 'Lab 2.1', 'Upcoming', 'https://via.placeholder.com/150?text=AI', '10:00:00', '13:00:00', ''),
(9, 6, 'Hackathon Night', '48-hour coding marathon challenge.', '2025-11-20', '2025-11-22', 'Hack Lab', 'Upcoming', 'https://via.placeholder.com/150?text=Hack', '18:00:00', '18:00:00', ''),
(10, 7, 'Robot Expo', 'Exhibition of student-built robots.', '2025-06-18', '2025-06-18', 'Exhibition Center', 'Upcoming', 'https://via.placeholder.com/150?text=Expo', '10:00:00', '14:00:00', ''),
(11, 7, 'Line Follower Contest', 'Test your autonomous robot designs.', '2025-07-22', '2025-07-22', 'Robotics Lab', 'Upcoming', 'https://via.placeholder.com/150?text=Contest', '11:00:00', '13:00:00', ''),
(12, 7, 'Arduino Basics', 'Intro workshop on Arduino microcontrollers.', '2025-08-30', '2025-08-30', 'Lab A1', 'Upcoming', 'https://via.placeholder.com/150?text=Arduino', '14:00:00', '16:00:00', ''),
(13, 8, 'Photography Walk', 'Capture campus moments with pros.', '2025-06-08', '2025-06-08', 'Campus Grounds', 'Upcoming', 'https://via.placeholder.com/150?text=Photo', '09:00:00', '11:00:00', ''),
(14, 8, 'Film Editing Workshop', 'Learn to edit like a pro.', '2025-07-10', '2025-07-10', 'Media Lab', 'Upcoming', 'https://via.placeholder.com/150?text=Edit', '13:00:00', '15:00:00', ''),
(15, 8, 'Short Film Festival', 'Premiere student short films.', '2025-09-01', '2025-09-01', 'Screening Room', 'Upcoming', 'https://via.placeholder.com/150?text=FilmFest', '18:00:00', '20:00:00', ''),
(16, 9, 'Tree Planting Drive', 'Plant trees around campus.', '2025-06-22', '2025-06-22', 'North Garden', 'Upcoming', 'https://via.placeholder.com/150?text=Planting', '08:00:00', '10:00:00', ''),
(17, 9, 'Recycling Workshop', 'Learn proper recycling techniques.', '2025-07-15', '2025-07-15', 'Eco Room', 'Upcoming', 'https://via.placeholder.com/150?text=Recycle', '10:00:00', '12:00:00', ''),
(18, 9, 'Clean-Up Challenge', 'Compete to clean the most waste.', '2025-08-20', '2025-08-20', 'Community Park', 'Upcoming', 'https://via.placeholder.com/150?text=CleanUp', '09:00:00', '11:30:00', ''),
(19, 10, 'Open Mic Night', 'Showcase your talent live.', '2025-06-25', '2025-06-25', 'Student Lounge', 'Upcoming', 'https://via.placeholder.com/150?text=OpenMic', '19:00:00', '21:00:00', ''),
(20, 10, 'Band Jam Session', 'Jamming session for student bands.', '2025-07-19', '2025-07-19', 'Music Room', 'Upcoming', 'https://via.placeholder.com/150?text=Jam', '17:00:00', '19:00:00', ''),
(21, 10, 'Beat Making 101', 'Intro to beat production.', '2025-08-14', '2025-08-14', 'Studio B', 'Upcoming', 'https://via.placeholder.com/150?text=Beats', '15:00:00', '17:00:00', ''),
(22, 11, 'Mental Health Talk', 'Discussion with campus therapists.', '2025-06-12', '2025-06-12', 'Wellness Hall', 'Upcoming', 'https://via.placeholder.com/150?text=Talk', '10:00:00', '12:00:00', ''),
(23, 11, 'Healthy Cooking Class', 'Learn nutritious recipes.', '2025-07-18', '2025-07-18', 'Cafeteria', 'Upcoming', 'https://via.placeholder.com/150?text=Cooking', '14:00:00', '16:00:00', ''),
(24, 11, 'Fitness Challenge', 'Push yourself in our summer challenge.', '2025-08-05', '2025-08-05', 'Gym Hall', 'Upcoming', 'https://via.placeholder.com/150?text=Fitness', '08:30:00', '10:30:00', ''),
(25, 12, 'Code Sprint', '1-day intense coding session.', '2025-06-30', '2025-06-30', 'Dev Lab', 'Upcoming', 'https://via.placeholder.com/150?text=Sprint', '10:00:00', '18:00:00', ''),
(26, 12, 'React.js Workshop', 'Build interactive UIs with React.', '2025-07-25', '2025-07-25', 'Room 204', 'Upcoming', 'https://via.placeholder.com/150?text=React', '13:00:00', '16:00:00', ''),
(27, 12, 'Git Mastery', 'Master version control with Git.', '2025-08-15', '2025-08-15', 'Tech Center', 'Upcoming', 'https://via.placeholder.com/150?text=Git', '11:00:00', '13:00:00', ''),
(28, 6, 'aaaa', 'aaaa', '2025-05-21', '2025-05-22', 'iset rades', 'Upcoming', '68285ba06d0fd7.92908939.jpg', '00:00:00', '02:30:00', '[{\"user_id\":9,\"username\":\"iyed hajri\",\"email\":\"iyedhajri@gmail.com\",\"isMemberOfClub\":false}]');

-- --------------------------------------------------------

--
-- Structure de la table `member`
--

CREATE TABLE `member` (
  `id` int(11) NOT NULL,
  `club_id` int(11) NOT NULL,
  `club_role` varchar(255) NOT NULL,
  `join_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `member`
--

INSERT INTO `member` (`id`, `club_id`, `club_role`, `join_at`) VALUES
(4, 6, 'manager', '2025-05-14'),
(5, 5, 'manager', '2025-05-01'),
(8, 6, 'member', '2025-05-14'),
(9, 5, 'member', '2025-05-16');

-- --------------------------------------------------------

--
-- Structure de la table `messenger_messages`
--

CREATE TABLE `messenger_messages` (
  `id` bigint(20) NOT NULL,
  `body` longtext NOT NULL,
  `headers` longtext NOT NULL,
  `queue_name` varchar(190) NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `available_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `delivered_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `email` varchar(180) NOT NULL,
  `roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '(DC2Type:json)' CHECK (json_valid(`roles`)),
  `password` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `dtype` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `email`, `roles`, `password`, `username`, `dtype`) VALUES
(1, 'talelbengharbia213@gmail.com', '[\"ROLE_ADMIN\"]', '$2y$13$zcjjPyc1J6aUQFnrFLngr.l6AHKEt900Y2y9No4r1b6fTLmqinMh2', 'talel', 'user'),
(2, 'exemple1@gmail.com', '[\"ROLE_USER\"]', '$2y$13$Kgh6XZdsup/mxpla2/5tseH7kAGwZZqxFbA9WMx7lamSEYLviEdJa', '', 'user'),
(4, 'exemple3@gmail.com', '[\"ROLE_USER\"]', '$2y$13$UKRDTqjayHZD6Q1OfHHl2eu6DLNdo.Z9MziezZFY7yNJwhY7ldcWa', 'exemple3', 'member'),
(5, 'ahmedmekki@gmail.com', '[\"ROLE_USER\"]', '$2y$13$ByIraLsgg62.00gqC57aW.bs5B3VN9NIPlB7CIi1zzPxAqf8B2YoG', 'ahmed mekki', 'member'),
(8, 'exemple2@gmail.com', '[\"ROLE_USER\"]', '$2y$13$tz3Be/v4pprL1NczEDrVde29jvKB91AzlDZ9ALMf3wv9HoPxxAuvK', 'exemple2', 'member'),
(9, 'iyedhajri@gmail.com', '[\"ROLE_USER\"]', '$2y$13$SxIvgvKx.ZrW9J99UtffF.vzFg3yU2Wb4hjRUBAMqVAC4r1vvgdMS', 'iyed hajri', 'member');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `club`
--
ALTER TABLE `club`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Index pour la table `event`
--
ALTER TABLE `event`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_3BAE0AA761190A32` (`club_id`);

--
-- Index pour la table `member`
--
ALTER TABLE `member`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_70E4FA7861190A32` (`club_id`);

--
-- Index pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  ADD KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  ADD KEY `IDX_75EA56E016BA31DB` (`delivered_at`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_IDENTIFIER_EMAIL` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `club`
--
ALTER TABLE `club`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `event`
--
ALTER TABLE `event`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `event`
--
ALTER TABLE `event`
  ADD CONSTRAINT `FK_3BAE0AA761190A32` FOREIGN KEY (`club_id`) REFERENCES `club` (`id`);

--
-- Contraintes pour la table `member`
--
ALTER TABLE `member`
  ADD CONSTRAINT `FK_70E4FA7861190A32` FOREIGN KEY (`club_id`) REFERENCES `club` (`id`),
  ADD CONSTRAINT `FK_70E4FA78BF396750` FOREIGN KEY (`id`) REFERENCES `user` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
