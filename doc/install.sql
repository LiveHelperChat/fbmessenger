CREATE TABLE `lhc_fbmessengerwhatsapp_message` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `user_id` bigint(20) unsigned NOT NULL,
    `created_at` bigint(20) unsigned NOT NULL,
    `updated_at` bigint(20) unsigned NOT NULL,
    `business_account_id` int(11) unsigned NOT NULL DEFAULT '0',
    `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
    `phone_sender` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
    `phone_sender_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
    `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
    `template` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
    `template_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
    `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
    `language` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
    `fb_msg_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
    `send_status_raw` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
    `conversation_id` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
    `dep_id` bigint(20) unsigned NOT NULL,
    `chat_id` bigint(20) unsigned NOT NULL,
    `initiation` bigint(20) unsigned NOT NULL,
    `scheduled_at` bigint(20) unsigned NOT NULL,
    `message_variables` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
    `campaign_recipient_id` bigint(20) unsigned NOT NULL,
    `campaign_id` bigint(20) unsigned NOT NULL,
    `private` tinyint(1) unsigned NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `fb_msg_id` (`fb_msg_id`),
    KEY `conversation_id` (`conversation_id`),
    KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `page_token` varchar(250) CHARACTER SET utf8mb4 NOT NULL,
  `verify_token` varchar(250) CHARACTER SET utf8mb4 NOT NULL,
  `name` varchar(250) CHARACTER SET utf8mb4 NOT NULL,
  `bot_disabled` tinyint(1) NOT NULL,
  `app_secret` varchar(250) CHARACTER SET utf8mb4 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `lhc_fbmessenger_bbcode` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `bbcode` varchar(50) CHARACTER SET utf8mb4 NOT NULL,
  `name` varchar(50) CHARACTER SET utf8mb4 NOT NULL,
  `configuration` text CHARACTER SET utf8mb4 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `lhc_fbmessenger_my_page` (
`id` bigint(20) NOT NULL AUTO_INCREMENT,
`page_id` bigint(20) NOT NULL,
`access_token` varchar(500) NOT NULL,
`enabled` int(11) NOT NULL,
`bot_disabled` tinyint(1) NOT NULL,
`dep_id` int(11) NOT NULL,
PRIMARY KEY (`id`),
KEY `page_id` (`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `lhc_fbmessenger_lead` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) NOT NULL DEFAULT 0,
  `dep_id` int(11) NOT NULL DEFAULT 0,
  `blocked` tinyint(1) NOT NULL DEFAULT 0,
  `page_id` bigint(20) NOT NULL DEFAULT 0,
  `user_id` bigint(20) NOT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile_pic` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile_pic_updated` int(11) NOT NULL DEFAULT 0,
  `locale` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `timezone` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ctime` int(11) DEFAULT NULL,
  `is_payment_enabled` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  KEY `blocked` (`blocked`),
  KEY `dep_id` (`dep_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `lhc_fbmessenger_fbuser` (
`id` bigint(20) NOT NULL AUTO_INCREMENT,
`user_id` int(11) NOT NULL,
`fb_user_id` bigint(20) NOT NULL,
`access_token` varchar(500) NOT NULL,
PRIMARY KEY (`id`),
KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `lhc_fbmessenger_notification_schedule` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `filter` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(50) CHARACTER SET utf8mb4 NOT NULL,
  `message` text CHARACTER SET utf8mb4 NOT NULL,
  `start_at` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `last_send` int(11) NOT NULL,
  `interval` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `lhc_fbmessenger_notification_schedule_campaign` ( `id` bigint(20) NOT NULL AUTO_INCREMENT, `schedule_id` bigint(20) NOT NULL, `status` int(11) NOT NULL, `last_id` int(11) NOT NULL, `ctime` int(11) NOT NULL, `last_send` int(11) NOT NULL, PRIMARY KEY (`id`), KEY `schedule_id` (`schedule_id`)) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `lhc_fbmessenger_notification_schedule_item` ( `id` bigint(20) NOT NULL AUTO_INCREMENT, `lead_id` bigint(20) NOT NULL, `status` int(11) NOT NULL, `log` text NOT NULL, `schedule_id` bigint(20) NOT NULL, `campaign_id` bigint(20) NOT NULL, `send_time` int(11) NOT NULL, PRIMARY KEY (`id`), KEY `campaign_id` (`campaign_id`), KEY `schedule_id` (`schedule_id`)) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `lhc_fbmessenger_standalone_fb_page` (
                      `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                      `page_id` bigint(20) NOT NULL,
                      `address` varchar(100) NOT NULL,
                      PRIMARY KEY (`id`),
                      UNIQUE KEY `page_id` (`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `lhc_fbmessengerwhatsapp_account` (
                                                   `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                                                   `dep_id` int(11) unsigned NOT NULL,
                                                   `business_account_id` bigint(20) unsigned NOT NULL,
                                                   `active` tinyint(1) NOT NULL,
                                                   `access_token` text COLLATE utf8mb4_unicode_ci NOT NULL,
                                                   `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
                                                   `phone_number_ids` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
                                                   `phone_number_deps` text NOT NULL,
                                                   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;