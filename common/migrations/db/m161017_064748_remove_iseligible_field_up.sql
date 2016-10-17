SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Drop field from `private_lesson`
-- ----------------------------
BEGIN;
ALTER TABLE `private_lesson` DROP COLUMN `isEligible`; 
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;