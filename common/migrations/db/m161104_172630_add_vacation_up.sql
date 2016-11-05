SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `vacation`
-- ----------------------------
BEGIN;
DROP TABLE IF EXISTS `vacation`;
CREATE TABLE `vacation` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `studentId` int(10) unsigned NOT NULL,
  `fromDate` timestamp NOT NULL,
  `toDate` timestamp NOT NULL,
  `isConfirmed` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8; 
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;

