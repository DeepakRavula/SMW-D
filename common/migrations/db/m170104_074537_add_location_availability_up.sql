SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `location_availability`
-- ----------------------------
BEGIN;
DROP TABLE IF EXISTS `location_availability`;
CREATE TABLE `location_availability` (
 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `locationId` int(10) unsigned NOT NULL,
 `day` tinyint(3) unsigned NOT NULL,
 `fromTime` time NOT NULL DEFAULT '00:00:00',
 `toTime` time NOT NULL DEFAULT '00:00:00',
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;


