SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Add field to `Invoice`
-- ----------------------------
BEGIN;
ALTER TABLE `invoice` ADD COLUMN `isSent` BOOLEAN NULL AFTER `reminderNotes`;
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
