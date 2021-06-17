SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `company`;
CREATE TABLE `company` (
    `id` INT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `phone_number` VARCHAR(20) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) UNIQUE NOT NULL,
    `deleted_at` DATETIME,
    `created_at` DATETIME NOT NULL DEFAULT NOW(),
    `updated_at` DATETIME
);
DROP TABLE IF EXISTS `role`;
CREATE TABLE `role` (
    `id` TINYINT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(100) NOT NULL,
    `description` TEXT NOT NULL,
    `slug` VARCHAR(255) NOT NULL,
    `company_id` INT UNSIGNED,
    `deleted_at` DATETIME,
    `created_at` DATETIME NOT NULL DEFAULT NOW(),
    `updated_at` DATETIME,
    FOREIGN KEY (`company_id`) REFERENCES `company` (`id`)
);
DROP TABLE IF EXISTS `department`;
CREATE TABLE `department` (
    `id` INT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL,
    `company_id` INT UNSIGNED NOT NULL,
    `deleted_at` DATETIME,
    `created_at` DATETIME NOT NULL DEFAULT NOW(),
    `updated_at` DATETIME,
    FOREIGN KEY (`company_id`) REFERENCES `company` (`id`)
);
DROP TABLE IF EXISTS `position`;
CREATE TABLE `position` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `description` TEXT,
    `department_id` INT UNSIGNED NOT NULL,
    `company_id` INT UNSIGNED NOT NULL,
    `deleted_at` DATETIME,
    `created_at` DATETIME NOT NULL DEFAULT NOW(),
    `updated_at` DATETIME,
    FOREIGN KEY (`department_id`) REFERENCES `department` (`id`),
    FOREIGN KEY (`company_id`) REFERENCES `company` (`id`)
);
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
    `id` INT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(50) NOT NULL,
    `email` VARCHAR(100) UNIQUE NOT NULL,
    `password` VARCHAR(128) NOT NULL,
    `company_id` INT UNSIGNED NOT NULL,
    `role_id` TINYINT UNSIGNED NOT NULL DEFAULT 2,
    `deleted_at` DATETIME,
    `created_at` DATETIME NOT NULL DEFAULT NOW(),
    `updated_at` DATETIME,
    FOREIGN KEY (`role_id`) REFERENCES `role` (`id`),
    FOREIGN KEY (`company_id`) REFERENCES `company` (`id`)
);
DROP TABLE IF EXISTS `employee`;
CREATE TABLE `employee` (
    `id` INT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
    `uuid` VARCHAR(64) UNIQUE NOT NULL,
    `employee_identifier` VARCHAR(100) UNIQUE,
    `employee_identifier_old` VARCHAR(100),
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) UNIQUE NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `birthdate` DATE NOT NULL,
    `phone_number` VARCHAR(20) UNIQUE NULL,
    `country_iso_code` VARCHAR(4) NOT NULL DEFAULT "ID",
    `country_iso_code_last` VARCHAR(4),
    `phone_number_last` VARCHAR(20),
    `department_id` INT UNSIGNED,
    `position_id` INT UNSIGNED,
    `company_id` INT UNSIGNED,
    `profile_picture` VARCHAR(64),
    `profile_picture_old` VARCHAR(64),
    `salary` INT UNSIGNED NOT NULL DEFAULT 0,
    `beneficiary_name` VARCHAR(255),
    `account_number` VARCHAR(100) NOT NULL,
    `bank_data_id` INT UNSIGNED NOT NULL,
    `pin_code` VARCHAR(64),
    `otp_request_token` VARCHAR(128),
    `remember_token` VARCHAR(255),
    `reset_token` VARCHAR(255),
    `user_id` INT UNSIGNED NOT NULL,
    `is_resigned` TINYINT NOT NULL DEFAULT 0,
    `resigned_at` DATE NULL,
    `deleted_at` DATETIME,
    `created_at` DATETIME NOT NULL DEFAULT NOW(),
    `updated_at` DATETIME,
    FOREIGN KEY (`department_id`) REFERENCES `department` (`id`),
    FOREIGN KEY (`position_id`) REFERENCES `position` (`id`),
    FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
    FOREIGN KEY (`company_id`) REFERENCES `company` (`id`)
);
DROP TABLE IF EXISTS `wallet`;
CREATE TABLE `wallet` (
    `id` INT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
    `employee_uuid` VARCHAR(64) NOT NULL,
    `amount` INT UNSIGNED NOT NULL DEFAULT 0,
    `deleted_at` DATETIME,
    `created_at` DATETIME NOT NULL DEFAULT NOW(),
    `updated_at` DATETIME
);

DROP TABLE IF EXISTS `bank_data`;
CREATE TABLE `bank_data` (
    `id` INT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
    `caption` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(128) NOT NULL,
    `swift_code` VARCHAR(32),
    `code` VARCHAR(32),
    `deleted_at` DATETIME,
    `created_at` DATETIME NOT NULL DEFAULT NOW(),
    `updated_at` DATETIME
);

-- ADD BANK DETAIL
DROP TABLE IF EXISTS `employee_request`;
CREATE TABLE `employee_request` (
    `id` INT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
    `employee_uuid` VARCHAR(64) NOT NULL,
    `self_withdraw` TINYINT NOT NULL DEFAULT 0 COMMENT '0 False 1 True',
    `company_id` INT UNSIGNED NOT NULL,
    `department_id` INT UNSIGNED NOT NULL,
    `amount` INT UNSIGNED NOT NULL DEFAULT 0,
    `fee` INT UNSIGNED NOT NULL DEFAULT 0,
    `total` INT UNSIGNED NOT NULL DEFAULT 0,
    `beneficiary_name` VARCHAR(100) NOT NULL,
    `account_number` VARCHAR(128) NOT NULL,
    `bank_data_id` INT UNSIGNED,
    `phone_number` VARCHAR(64),
    `status` TINYINT NOT NULL DEFAULT 0 COMMENT '0 pending 1 approved 2 declined',
    `deleted_at` DATETIME,
    `created_at` DATETIME NOT NULL DEFAULT NOW(),
    `updated_at` DATETIME,
    `approved_at` DATETIME,
    FOREIGN KEY (`company_id`) REFERENCES `company` (`id`),
    FOREIGN KEY (`department_id`) REFERENCES `department` (`id`)
);
DROP TABLE IF EXISTS `configuration`;
CREATE TABLE `configuration` (
    `id` INT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
    `key` VARCHAR(255) NOT NULL UNIQUE,
    `data` json NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `deleted_at` DATETIME,
    `created_at` DATETIME NOT NULL DEFAULT NOW(),
    `updated_at` DATETIME
);

INSERT INTO `configuration` (`id`, `key`, `data`, `slug`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'PUBLIC TOKEN', '{\"token\": \"p723uxxUEhKcyU2w2UfY\"}', 'public_token', NULL, '2021-06-01 10:44:59', NULL),
(2, 'IMPORTANT DATE', '[{\"company_id\":1,\"cut_off\":20,\"payday\":28},{\"company_id\":2,\"cut_off\":20,\"payday\":28},{\"company_id\":3,\"cut_off\":20,\"payday\":28},{\"company_id\":4,\"cut_off\":20,\"payday\":28},{\"company_id\":5,\"cut_off\":20,\"payday\":28},{\"company_id\":6,\"cut_off\":20,\"payday\":28},{\"company_id\":7,\"cut_off\":20,\"payday\":28},{\"company_id\":8,\"cut_off\":20,\"payday\":28},{\"company_id\":9,\"cut_off\":20,\"payday\":28},{\"company_id\":10,\"cut_off\":20,\"payday\":28}]', 'important_date', NULL, '2021-06-05 20:04:16', NULL),
(3, 'HOLIDAYS', '{\"id\": [{\"date\": \"2021-05-26\", \"exclude\": [], \"description\": \"Waisak\"}, {\"date\": \"2021-06-01\", \"exclude\": [], \"description\": \"Pancasila\"}]}', 'holidays', NULL, '2021-06-05 20:34:51', NULL),
(4, 'FREE TRANSACTION', '{\"value\": 11}', 'free_transaction', NULL, '2021-06-12 02:01:34', NULL);

DROP TABLE IF EXISTS `otp_code`;
CREATE TABLE `otp_code` (
    `id` INT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
    `employee_uuid` VARCHAR(64) NOT NULL,
    `code` INT UNSIGNED NOT NULL,
    `attempt` TINYINT UNSIGNED NOT NULL DEFAULT 0,
    `deleted_at` DATETIME,
    `created_at` DATETIME NOT NULL DEFAULT NOW(),
    `updated_at` DATETIME,
    FOREIGN KEY (`employee_uuid`) REFERENCES `employee` (`uuid`)
);

DROP TABLE IF EXISTS `guest_book`;
CREATE TABLE `guest_book` (
    `id` INT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
    `phone_number` VARCHAR(32) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `deleted_at` DATETIME,
    `created_at` DATETIME NOT NULL DEFAULT NOW(),
    `updated_at` DATETIME
);

DROP TABLE IF EXISTS `bank_list`;
CREATE TABLE `bank_list` (
  `bank_id` INT NOT NULL AUTO_INCREMENT,
  `bank_position` INT DEFAULT NULL,
  `bank_name` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `bank_name_iris` VARCHAR(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_code` VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_image` VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_flg_available` INT DEFAULT '0',
  `bank_create_by` VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_create_date` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `bank_update_by` VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_update_date` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `bank_del_status` ENUM('0','1') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '0 ada 1 hapus',
  PRIMARY KEY (`bank_id`) USING BTREE,
  KEY `bank_id` (`bank_id`) USING BTREE,
  KEY `bank_name` (`bank_name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=158 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `last_contact`;
CREATE TABLE `last_contact` (
    `id` INT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
    `employee_uuid` VARCHAR(64) NOT NULL UNIQUE,
    `beneficiary_name` VARCHAR(255) NOT NULL,
    `account_number` VARCHAR(100) NOT NULL,
    `bank_data_id` INT,
    `phone_number` VARCHAR(64),
    `deleted_at` DATETIME,
    `created_at` DATETIME NOT NULL DEFAULT NOW(),
    `updated_at` DATETIME
);

DROP TABLE IF EXISTS `notification`;
CREATE TABLE `notification` (
    `id` INT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
    `notifiable_type` VARCHAR(200) NOT NULL,
    `notifiable_id` VARCHAR(255) NOT NULL,
    `title` VARCHAR(100) NOT NULL,
    `content` VARCHAR(100) NOT NULL,
    `state` TINYINT NOT NULL DEFAULT 0,
    `deleted_at` DATETIME,
    `created_at` DATETIME NOT NULL DEFAULT NOW(),
    `updated_at` DATETIME
);

DROP TABLE IF EXISTS `device`;
CREATE TABLE `device` (
    `id` INT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
    `employee_uuid` VARCHAR(100) NOT NULL,
    `device_token` VARCHAR(100) NOT NULL,
    `hash` VARCHAR(255) NOT NULL UNIQUE,
    `deleted_at` DATETIME,
    `created_at` DATETIME NOT NULL DEFAULT NOW(),
    `updated_at` DATETIME
);

SET FOREIGN_KEY_CHECKS = 1;