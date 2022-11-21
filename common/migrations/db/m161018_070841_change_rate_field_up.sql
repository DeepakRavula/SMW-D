SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Modify field from `program`
-- ----------------------------
BEGIN;
ALTER TABLE  `program` CHANGE `rate` `rate` DECIMAL(10,2) NULL;
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;