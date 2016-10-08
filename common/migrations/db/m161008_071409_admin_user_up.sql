SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Master rows of `city`
-- ----------------------------
BEGIN;
insert  into `rbac_auth_assignment`(`item_name`,`user_id`,`created_at`) values ('administrator','1',1461762156);
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;