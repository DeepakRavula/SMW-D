/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `professional_development_day` */

DROP TABLE IF EXISTS `professional_development_day`;

CREATE TABLE `professional_development_day` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `professional_development_day` */

insert  into `professional_development_day`(`id`,`date`) values (1,'2016-01-30 00:00:00'),(2,'2016-02-29 00:00:00'),(3,'2016-03-29 00:00:00'),(4,'2016-03-30 00:00:00'),(5,'2016-03-31 00:00:00'),(6,'2016-04-29 00:00:00'),(7,'2016-04-30 00:00:00'),(8,'2016-05-30 00:00:00'),(9,'2016-05-31 00:00:00'),(10,'2016-06-29 00:00:00'),(11,'2016-06-30 00:00:00'),(12,'2016-07-30 00:00:00'),(13,'2016-08-30 00:00:00'),(14,'2016-08-31 00:00:00'),(15,'2016-09-29 00:00:00'),(16,'2016-09-30 00:00:00'),(17,'2016-10-29 00:00:00'),(18,'2016-10-31 00:00:00'),(19,'2016-10-29 00:00:00'),(20,'2016-11-30 00:00:00'),(21,'2016-12-29 00:00:00'),(22,'2016-12-30 00:00:00'),(23,'2016-12-31 00:00:00');