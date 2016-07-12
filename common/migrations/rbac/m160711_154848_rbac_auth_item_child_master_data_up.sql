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
/*Table structure for table `rbac_auth_item_child` */

DROP TABLE IF EXISTS `rbac_auth_item_child`;

CREATE TABLE `rbac_auth_item_child` (
  `parent` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `child` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`parent`,`child`),
  KEY `child` (`child`),
  CONSTRAINT `rbac_auth_item_child_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `rbac_auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rbac_auth_item_child_ibfk_2` FOREIGN KEY (`child`) REFERENCES `rbac_auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `rbac_auth_item_child` */

insert  into `rbac_auth_item_child`(`parent`,`child`) values ('owner','createStaff'),('staffmember','deleteCustomerProfile'),('administrator','deleteOwnerProfile'),('owner','deleteStaffProfile'),('staffmember','deleteTeacherProfile'),('staffmember','loginToBackend'),('administrator','owner'),('administrator','staffmember'),('owner','staffmember'),('staffmember','updateCustomerProfile'),('administrator','updateOwnerProfile'),('administrator','updateOwnProfile'),('owner','updateOwnProfile'),('staffmember','updateOwnProfile'),('owner','updateStaffProfile'),('staffmember','updateTeacherProfile');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
