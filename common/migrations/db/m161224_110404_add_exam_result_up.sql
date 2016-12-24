SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `exam_result`
-- ----------------------------
BEGIN;
DROP TABLE IF EXISTS `exam_result`;
CREATE TABLE `exam_result` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `studentId` int(10) unsigned NOT NULL,
  `date` timestamp NOT NULL,
  `mark` tinyint(10) unsigned NOT NULL,
  `level` tinyint(3) unsigned NOT NULL,
  `program` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `teacherId` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;

