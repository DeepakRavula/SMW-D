SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `invoice_discount``
-- ----------------------------
CREATE TABLE `invoice_discount` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `invoiceId` INT(10) UNSIGNED NOT NULL,
  `value` DECIMAL(10,0) UNSIGNED NOT NULL,
  `valueType` TINYINT(3) UNSIGNED NOT NULL,
  `type` TINYINT(3) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=INNODB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


SET FOREIGN_KEY_CHECKS = 1;