SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Drop field from `note`
-- ----------------------------
BEGIN;
ALTER TABLE `note` DROP COLUMN `updatedUserId`; 
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;