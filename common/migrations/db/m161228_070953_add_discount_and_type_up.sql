SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Add field to `line_item`
-- ----------------------------
BEGIN;
ALTER TABLE `invoice_line_item` ADD COLUMN `discount` FLOAT UNSIGNED NOT NULL AFTER `tax_code`,
ADD COLUMN `discountType` TINYINT UNSIGNED NOT NULL AFTER `discount`;
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
