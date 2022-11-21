SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Modify field from `invoice_line_item`
-- ----------------------------
BEGIN;
ALTER TABLE `invoice_line_item` CHANGE `tax_rate` `tax_rate` DECIMAL(10,2) NOT NULL; 
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;

