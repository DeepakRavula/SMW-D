SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Modify field from `payment`
-- ----------------------------
BEGIN;
ALTER TABLE `payment` CHANGE `reference` `reference` VARCHAR(20) NULL;
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
