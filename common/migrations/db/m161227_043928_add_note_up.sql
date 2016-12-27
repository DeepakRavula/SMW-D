SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `note`
-- ----------------------------
BEGIN;
DROP TABLE IF EXISTS `note`;
CREATE TABLE `note` (
 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `instanceId` int(10) unsigned NOT NULL,
 `instanceType` tinyint(3) unsigned NOT NULL,
 `content` text COLLATE utf8_unicode_ci NOT NULL,
 `createdUserId` int(10) unsigned NOT NULL,
 `updatedUserId` int(10) unsigned NOT NULL,
 `createdOn` timestamp NOT NULL,
 `updatedOn` timestamp NOT NULL ,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;



