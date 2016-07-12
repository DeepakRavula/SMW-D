
/* Foreign Keys must be dropped in the target to ensure that requires changes can be done*/

ALTER TABLE `user_profile` 
	DROP FOREIGN KEY `fk_user`  ;


/* Create table in target */
CREATE TABLE `address`(
	`id` int(11) unsigned NOT NULL  auto_increment , 
	`label` varchar(32) COLLATE utf8_general_ci NOT NULL  , 
	`address` varchar(64) COLLATE utf8_general_ci NOT NULL  , 
	`city_id` int(11) NOT NULL  , 
	`province_id` int(11) NOT NULL  , 
	`postal_code` varchar(16) COLLATE utf8_general_ci NOT NULL  , 
	`country_id` int(11) NOT NULL  , 
	`is_primary` tinyint(3) unsigned NOT NULL  DEFAULT 0 , 
	PRIMARY KEY (`id`) , 
	KEY `city_id`(`city_id`) , 
	KEY `city_id_2`(`city_id`) 
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';


/* Create table in target */
CREATE TABLE `city`(
	`id` int(11) NOT NULL  auto_increment , 
	`name` varchar(32) COLLATE utf8_general_ci NOT NULL  , 
	`province_id` int(11) NOT NULL  , 
	PRIMARY KEY (`id`) , 
	UNIQUE KEY `id`(`id`) 
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';


/* Create table in target */
CREATE TABLE `country`(
	`id` int(11) NOT NULL  auto_increment , 
	`name` varchar(32) COLLATE utf8_general_ci NOT NULL  , 
	PRIMARY KEY (`id`) 
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';


/* Create table in target */
CREATE TABLE `enrolment`(
	`id` int(10) unsigned NOT NULL  auto_increment , 
	`student_id` int(10) unsigned NOT NULL  , 
	`qualification_id` int(10) unsigned NOT NULL  , 
	`commencement_date` timestamp(3) NOT NULL  DEFAULT CURRENT_TIMESTAMP(3) on update CURRENT_TIMESTAMP(3) , 
	`renewal_date` timestamp NOT NULL  DEFAULT '0000-00-00 00:00:00' , 
	`location_id` int(10) unsigned NOT NULL  , 
	PRIMARY KEY (`id`) 
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_unicode_ci';


/* Create table in target */
CREATE TABLE `enrolment_schedule_day`(
	`id` int(10) unsigned NOT NULL  auto_increment , 
	`enrolment_id` int(10) unsigned NOT NULL  , 
	`day` tinyint(3) unsigned NOT NULL  , 
	`from_time` time NOT NULL  , 
	`duration` time NOT NULL  , 
	`to_time` time NOT NULL  , 
	PRIMARY KEY (`id`) 
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_unicode_ci';


/* Create table in target */
CREATE TABLE `invoice`(
	`id` int(11) unsigned NOT NULL  auto_increment , 
	`invoice_number` int(11) unsigned NOT NULL  , 
	`date` timestamp NULL  , 
	`status` tinyint(3) unsigned NOT NULL  COMMENT '1 - paid,2 - owing,3 - credit' , 
	`subTotal` decimal(10,2) NULL  , 
	`tax` decimal(10,2) NULL  , 
	`total` decimal(10,2) NULL  , 
	`notes` text COLLATE utf8_unicode_ci NULL  , 
	`internal_notes` text COLLATE utf8_unicode_ci NULL  , 
	PRIMARY KEY (`id`) 
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_unicode_ci';


/* Create table in target */
CREATE TABLE `invoice_line_item`(
	`id` int(10) unsigned NOT NULL  auto_increment , 
	`invoice_id` int(10) unsigned NOT NULL  , 
	`lesson_id` int(10) unsigned NOT NULL  , 
	`unit` float NOT NULL  , 
	`amount` decimal(10,2) NOT NULL  , 
	PRIMARY KEY (`id`) 
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_unicode_ci';


/* Create table in target */
CREATE TABLE `lesson`(
	`id` int(10) unsigned NOT NULL  auto_increment , 
	`enrolment_schedule_day_id` int(10) unsigned NOT NULL  , 
	`status` tinyint(3) unsigned NOT NULL  COMMENT '1 - completed; 2 - pending; 3 - canceled' , 
	`date` timestamp NOT NULL  DEFAULT CURRENT_TIMESTAMP , 
	`notes` text COLLATE utf8_unicode_ci NULL  , 
	PRIMARY KEY (`id`) 
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_unicode_ci';


/* Create table in target */
CREATE TABLE `location`(
	`id` int(11) unsigned NOT NULL  auto_increment , 
	`name` varchar(32) COLLATE utf8_unicode_ci NOT NULL  , 
	`address` varchar(64) COLLATE utf8_unicode_ci NOT NULL  , 
	`phone_number` varchar(16) COLLATE utf8_unicode_ci NOT NULL  , 
	`city_id` int(11) unsigned NOT NULL  , 
	`province_id` int(11) unsigned NOT NULL  , 
	`postal_code` varchar(16) COLLATE utf8_unicode_ci NOT NULL  , 
	`country_id` int(11) unsigned NOT NULL  , 
	`from_time` time NOT NULL  , 
	`to_time` time NOT NULL  , 
	PRIMARY KEY (`id`) 
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_unicode_ci';


/* Create table in target */
CREATE TABLE `payment_methods`(
	`id` tinyint(3) unsigned NOT NULL  auto_increment , 
	`name` varchar(30) COLLATE latin1_swedish_ci NOT NULL  , 
	PRIMARY KEY (`id`) 
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_unicode_ci';


/* Create table in target */
CREATE TABLE `payments`(
	`id` int(10) unsigned NOT NULL  auto_increment , 
	`user_id` int(10) unsigned NOT NULL  , 
	`invoice_id` int(10) unsigned NOT NULL  COMMENT '1 - prepymanet - 2+ real invoices payment' , 
	`payment_method_id` tinyint(3) unsigned NOT NULL  , 
	`amount` double unsigned NOT NULL  , 
	PRIMARY KEY (`id`) 
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_unicode_ci';


/* Create table in target */
CREATE TABLE `phone_label`(
	`id` int(11) NOT NULL  auto_increment , 
	`name` varchar(32) COLLATE utf8_general_ci NOT NULL  , 
	PRIMARY KEY (`id`) 
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';


/* Create table in target */
CREATE TABLE `phone_number`(
	`id` int(11) NOT NULL  auto_increment , 
	`user_id` int(11) NOT NULL  , 
	`label_id` int(11) NOT NULL  , 
	`number` varchar(16) COLLATE utf8_general_ci NOT NULL  , 
	`extension` int(4) NULL  , 
	`is_primary` tinyint(3) unsigned NOT NULL  DEFAULT 0 , 
	PRIMARY KEY (`id`) , 
	KEY `user_id`(`user_id`) 
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';


/* Create table in target */
CREATE TABLE `program`(
	`id` int(10) unsigned NOT NULL  auto_increment , 
	`name` varchar(30) COLLATE utf8_unicode_ci NOT NULL  , 
	`rate` int(11) NULL  , 
	`status` tinyint(3) unsigned NULL  COMMENT '1 - active; 2 - inactive' , 
	PRIMARY KEY (`id`) 
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_unicode_ci';


/* Create table in target */
CREATE TABLE `province`(
	`id` int(11) NOT NULL  auto_increment , 
	`name` varchar(16) COLLATE utf8_general_ci NOT NULL  , 
	`tax_rate` double NOT NULL  , 
	`country_id` int(11) NOT NULL  , 
	PRIMARY KEY (`id`) 
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';


/* Create table in target */
CREATE TABLE `provinces`(
	`id` int(11) NOT NULL  auto_increment , 
	`name` varchar(16) COLLATE utf8_general_ci NOT NULL  , 
	`tax_rate` double NOT NULL  , 
	`country_id` int(11) NOT NULL  , 
	PRIMARY KEY (`id`) 
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';


/* Create table in target */
CREATE TABLE `qualification`(
	`id` int(11) unsigned NOT NULL  auto_increment , 
	`teacher_id` int(11) unsigned NOT NULL  , 
	`program_id` int(11) unsigned NOT NULL  , 
	PRIMARY KEY (`id`) 
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_unicode_ci';


/* Create table in target */
CREATE TABLE `student`(
	`id` int(11) NOT NULL  auto_increment , 
	`first_name` varchar(30) COLLATE utf8_unicode_ci NOT NULL  , 
	`last_name` varchar(30) COLLATE utf8_unicode_ci NOT NULL  , 
	`birth_date` date NULL  , 
	`customer_id` int(11) NULL  , 
	`notes` text COLLATE utf8_unicode_ci NULL  , 
	PRIMARY KEY (`id`) 
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_unicode_ci';


/* Create table in target */
CREATE TABLE `tax`(
	`id` int(11) NOT NULL  auto_increment , 
	`province_id` int(11) NOT NULL  , 
	`tax_rate` double NOT NULL  , 
	`since` date NOT NULL  , 
	PRIMARY KEY (`id`) , 
	KEY `id`(`id`) 
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_unicode_ci';


/* Create table in target */
CREATE TABLE `teacher_availability_day`(
	`id` int(10) unsigned NOT NULL  auto_increment , 
	`teacher_location_id` int(10) unsigned NOT NULL  , 
	`day` tinyint(3) unsigned NOT NULL  , 
	`from_time` time NOT NULL  , 
	`to_time` time NOT NULL  , 
	PRIMARY KEY (`id`) 
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_unicode_ci';


/* Alter table in target */
ALTER TABLE `user` 
	CHANGE `password_hash` `password_hash` varchar(255)  COLLATE utf8_unicode_ci NULL after `access_token` ;

/* Create table in target */
CREATE TABLE `user_address`(
	`id` int(10) unsigned NOT NULL  auto_increment , 
	`user_id` int(10) unsigned NOT NULL  , 
	`address_id` int(10) unsigned NOT NULL  , 
	PRIMARY KEY (`id`) 
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_unicode_ci';


/* Create table in target */
CREATE TABLE `user_location`(
	`id` int(10) unsigned NOT NULL  auto_increment , 
	`user_id` int(10) unsigned NOT NULL  , 
	`location_id` int(10) unsigned NOT NULL  , 
	PRIMARY KEY (`id`) 
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_unicode_ci';


/* Alter table in target */
ALTER TABLE `user_profile` 
	ADD COLUMN `notes` text  COLLATE utf8_unicode_ci NULL after `lastname` , 
	CHANGE `avatar_path` `avatar_path` varchar(255)  COLLATE utf8_unicode_ci NULL after `notes` ; 

/* The foreign keys that were dropped are now re-created*/

ALTER TABLE `user_profile` 
	ADD CONSTRAINT `fk_user` 
	FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;

/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
