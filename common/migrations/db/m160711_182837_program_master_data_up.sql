/*
SQLyog Ultimate v12.08 (32 bit)
MySQL - 5.6.26 : Database - kg-smw2
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `program` */

DROP TABLE IF EXISTS `program`;

CREATE TABLE `program` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `rate` int(11) DEFAULT NULL,
  `status` tinyint(3) unsigned DEFAULT NULL COMMENT '1 - active; 2 - inactive',
  `type` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci

/*Data for the table `program` */

insert  into `program`(`id`,`name`,`rate`,`status`, `type`) values (1,'piano',200,1,1),(2,'flute',150,1,1),(3,'guitar',300,1,1),(4,'violin',300,1,1),(5,'trumpet',200,1,1), (6,'trumpet theory',200,1,2), (7,'flute class',200,1,2), (8,'piano band',200,1,2), (9,'cello class',500,1,2),;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
