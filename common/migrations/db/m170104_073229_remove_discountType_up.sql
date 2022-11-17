SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Drop field from `invoice_line_item`
-- ----------------------------
BEGIN;
ALTER TABLE `invoice_line_item` DROP COLUMN `discountType`;
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;