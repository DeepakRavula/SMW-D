SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `discount`
-- ----------------------------
BEGIN;
DROP TABLE IF EXISTS `discount`;
CREATE TABLE `discount` (
 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `lineItemId` int(10) unsigned NOT NULL,
 `discount` float unsigned NOT NULL,
 `type` tinyint(4) NOT NULL COMMENT '1- percentage; 2- flat;',
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;


