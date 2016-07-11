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
/*Table structure for table `location` */

DROP TABLE IF EXISTS `location`;

CREATE TABLE `location` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `address` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `phone_number` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `city_id` int(11) unsigned NOT NULL,
  `province_id` int(11) unsigned NOT NULL,
  `postal_code` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `country_id` int(11) unsigned NOT NULL,
  `from_time` time NOT NULL,
  `to_time` time NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `location` */

insert  into `location`(`id`,`name`,`address`,`phone_number`,`city_id`,`province_id`,`postal_code`,`country_id`,`from_time`,`to_time`) values (1,'Arcadia Corporate','205 Marycroft Ave., Unit 6, Woodbridge','905-254-3424',1,1,'L4L 5X8',1,'08:00:00','18:00:00'),(2,'Newmarket','1670 Bayview Ave. Unit B102-105, Newmarket','417-254-3425',1,1,'L3X 1W1',1,'09:00:00','22:00:00'),(3,'South Brampton','16700 Bayview Ave. Unit B102-105, Newmarket','(905)254-3424',1,1,'L3X 1W1',1,'06:00:00','17:00:00'),(4,'Bolton','12 Parr Blvd., Unit 7 & 8, Bolton','805-254-3424',1,1,'L7E 4H1',1,'07:30:00','19:30:00'),(5,'North Brampton','9960 McVean Rd., Unit 4, Brampton','705-254-3424',1,1,'L6P 2S5',1,'08:30:00','20:00:00'),(6,'West Brampton','10625 Creditview Rd., Unit 3-C, Brampton','(678) 254-3424',1,1,'L7A 3A4',1,'10:00:00','23:30:00'),(7,'Maple','2620 Rutherford Rd., Unit 5,6,7, Vibrant Square, Maple','905-254-9673',1,1,'L4K 0H1',1,'06:00:00','18:00:00'),(8,'Richmond Hill','10909 Yonge St., Unit 8, Richmond Hill','978-254-3424',1,1,'L4C 3E3',1,'07:00:00','19:00:00'),(9,'Woodbridge','205 Marycroft Ave., Unit 6, Woodbridge','587-254-3424',1,1,'L4L 5X8',1,'08:00:00','21:00:00');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
