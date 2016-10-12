SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Master rows of `rbac_auth_assignment`
-- ----------------------------
BEGIN;
INSERT  INTO `rbac_auth_assignment`(`item_name`,`user_id`,`created_at`) VALUES ('administrator','1',1461762156);
COMMIT;

-- ----------------------------
--  Master rows of `user`
-- ----------------------------
BEGIN;
INSERT  INTO `user`(`id`,`username`,`auth_key`,`access_token`,`password_hash`,`oauth_client`,`oauth_client_user_id`,`email`,`status`,`created_at`,`updated_at`,`logged_at`) VALUES (1,'Kristin','k8K70rZiG4h4Q226Yld4q6qullXDHRED','QuYNM0UsI1rWWWr2ru6Zy6pJAOHP4i1XYL5havfM','$2y$13$ez87O4QuFNNTgbh/M1LiL.2hh2o.LBNsYN9rcaJ5UwVBBYwDmlvvi',NULL,NULL,'kristin@kristingreen.ca',2,1461825793,1467730273,1476176094);
COMMIT;

-- ----------------------------
--  Master rows of `user_profile`
-- ----------------------------
BEGIN;
INSERT  INTO `user_profile`(`user_id`,`firstname`,`middlename`,`lastname`,`notes`,`avatar_path`,`avatar_base_url`,`locale`,`gender`) VALUES (1,'Kristin','','Green','',NULL,NULL,'en-US',1);
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;