SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Add field to `student_csv`
-- ----------------------------
BEGIN;
ALTER TABLE `student_csv` ADD COLUMN `openingBalance` DECIMAL(10,4) NULL AFTER `billingWorkTelExt`, ADD COLUMN `notes` TEXT NULL AFTER `openingBalance`; 
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;