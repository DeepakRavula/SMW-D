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
/*Table structure for table `rbac_auth_rule` */

DROP TABLE IF EXISTS `rbac_auth_rule`;

CREATE TABLE `rbac_auth_rule` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `data` text COLLATE utf8_unicode_ci,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `rbac_auth_rule` */

insert  into `rbac_auth_rule`(`name`,`data`,`created_at`,`updated_at`) values ('ownModelRule','O:29:\"common\\rbac\\rule\\OwnModelRule\":3:{s:4:\"name\";s:12:\"ownModelRule\";s:9:\"createdAt\";i:1461762156;s:9:\"updatedAt\";i:1461762156;}',1461762156,1461762156),('updateOwnProfileRule','O:37:\"common\\rbac\\rule\\UpdateOwnProfileRule\":3:{s:4:\"name\";s:20:\"updateOwnProfileRule\";s:9:\"createdAt\";i:1464932434;s:9:\"updatedAt\";i:1464932434;}',1464932434,1464932434);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
