CREATE TABLE `lhc_fbmessenger_chat` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `recipient_user_id` bigint(20) NOT NULL,
  `chat_id` bigint(20) NOT NULL,
  `ctime` int(11) NOT NULL,
  `page_id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id_recipient_user_id` (`user_id`,`recipient_user_id`),
  KEY `chat_id` (`chat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `lhc_fbmessenger_page` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `dep_id` int(11) NOT NULL,
  `verified` int(11) NOT NULL,
  `page_token` varchar(250) NOT NULL,
  `verify_token` varchar(250) NOT NULL,
  `name` varchar(250) NOT NULL,
  `app_secret` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `lhc_fbmessenger_bbcode` (`id` bigint(20) NOT NULL AUTO_INCREMENT, `bbcode` varchar(50) NOT NULL, `name` varchar(50) NOT NULL, `configuration` text NOT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;