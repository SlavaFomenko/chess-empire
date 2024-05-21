CREATE TABLE `user` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `role` varchar(50),
  `email` varchar(255),
  `password` varchar(255),
  `username` varchar(100),
  `first_name` varchar(50),
  `last_name` varchar(50),
  `rating` int
);

CREATE TABLE `friend_pair` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `user1_id` int,
  `user2_id` int,
  `accepted` bool
);

CREATE TABLE `game` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `time` int,
  `rated` bool,
  `winner` varchar(1),
  `b_rating` int,
  `w_rating` int,
  `b_id` int,
  `w_id` int,
  `history` text,
  `played_date` bigint
);

ALTER TABLE `friend_pair` ADD FOREIGN KEY (`user1_id`) REFERENCES `user` (`id`);

ALTER TABLE `friend_pair` ADD FOREIGN KEY (`user2_id`) REFERENCES `user` (`id`);

ALTER TABLE `game` ADD FOREIGN KEY (`b_id`) REFERENCES `user` (`id`);

ALTER TABLE `game` ADD FOREIGN KEY (`w_id`) REFERENCES `user` (`id`);