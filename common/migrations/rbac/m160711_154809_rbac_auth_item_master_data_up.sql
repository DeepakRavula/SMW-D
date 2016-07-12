/*
SQLyog Ultimate v12.08 (32 bit)
MySQL - 5.6.26 : Database - kg-smw2-may19
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `rbac_auth_item` */

DROP TABLE IF EXISTS `rbac_auth_item`;

CREATE TABLE `rbac_auth_item` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `type` int(11) NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `rule_name` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `data` text COLLATE utf8_unicode_ci,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`),
  KEY `rule_name` (`rule_name`),
  KEY `idx-auth_item-type` (`type`),
  CONSTRAINT `rbac_auth_item_ibfk_1` FOREIGN KEY (`rule_name`) REFERENCES `rbac_auth_rule` (`name`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `rbac_auth_item` */

insert  into `rbac_auth_item`(`name`,`type`,`description`,`rule_name`,`data`,`created_at`,`updated_at`) values ('administrator',1,'Administrator',NULL,NULL,1461762156,1466070495),('createStaff',2,NULL,NULL,NULL,1464932683,1464932683),('customer',1,'Customer',NULL,NULL,1462108076,1466070534),('deleteCustomerProfile',2,NULL,NULL,NULL,1465363736,1465363736),('deleteOwnerProfile',2,NULL,NULL,NULL,1465363769,1465363769),('deleteStaffProfile',2,NULL,NULL,NULL,1465374421,1465374421),('deleteTeacherProfile',2,NULL,NULL,NULL,1465363796,1465363796),('editOwnModel',2,NULL,'ownModelRule',NULL,1461762156,1461762156),('loginToBackend',2,NULL,NULL,NULL,1461762156,1461762156),('owner',1,'Owner',NULL,NULL,1464945513,1466070583),('staffmember',1,'Staff Member',NULL,NULL,1464260067,1465383666),('teacher',1,'Teacher',NULL,NULL,1462267027,1466070737),('updateCustomerProfile',2,NULL,NULL,NULL,1465363815,1465363815),('updateOwnerProfile',2,NULL,NULL,NULL,1465363833,1465363833),('updateOwnProfile',2,NULL,'updateOwnProfileRule',NULL,1464932753,1464932753),('updateStaffProfile',2,NULL,NULL,NULL,1465363845,1465363845),('updateTeacherProfile',2,NULL,NULL,NULL,1465363921,1465363921);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
