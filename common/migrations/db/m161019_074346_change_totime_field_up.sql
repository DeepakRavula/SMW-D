SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Modify field from `lesson`
-- ----------------------------
BEGIN;
ALTER TABLE `lesson` CHANGE `toTime` `duration` TIME NOT NULL;
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;