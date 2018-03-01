CREATE TABLE `lhc_fbmessenger_chat` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `recipient_user_id` bigint(20) NOT NULL,
  `chat_id` bigint(20) NOT NULL,
  `ctime` int(11) NOT NULL,
  `page_id` bigint(20) NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `user_id_recipient_user_id` (`user_id`,`recipient_user_id`),
  KEY `chat_id` (`chat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `lhc_fbmessenger_page` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `dep_id` int(11) NOT NULL,
  `verified` int(11) NOT NULL,
  `page_token` varchar(250) NOT NULL,
  `verify_token` varchar(250) NOT NULL,
  `name` varchar(250) NOT NULL,
  `app_secret` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `lhc_fbmessenger_bbcode` (
`id` bigint(20) NOT NULL AUTO_INCREMENT,
`bbcode` varchar(50) NOT NULL,
`name` varchar(50) NOT NULL,
`configuration` text NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `lhc_fbmessenger_my_page` (
`id` bigint(20) NOT NULL AUTO_INCREMENT,
`page_id` bigint(20) NOT NULL,
`access_token` varchar(250) NOT NULL,
`enabled` int(11) NOT NULL,
`dep_id` int(11) NOT NULL,
PRIMARY KEY (`id`),
KEY `page_id` (`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `lhc_fbmessenger_lead` (
`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
`type` tinyint(1) NOT NULL,
`dep_id` int(11) NOT NULL,
`page_id` bigint(20) NOT NULL,
`user_id` bigint(20) NOT NULL,
`first_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`last_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`profile_pic` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`profile_pic_updated`int(11) NOT NULL,
`locale` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`timezone` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`gender` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`country` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`ctime` int(11) NULL DEFAULT NULL,
`is_payment_enabled` tinyint(1) NULL DEFAULT 0,
PRIMARY KEY (`id`),
UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `lhc_fbmessenger_fbuser` (
`id` bigint(20) NOT NULL AUTO_INCREMENT,
`user_id` int(11) NOT NULL,
`fb_user_id` bigint(20) NOT NULL,
`access_token` varchar(250) NOT NULL,
PRIMARY KEY (`id`),
KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;