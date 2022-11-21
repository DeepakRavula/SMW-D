SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

/*Data for the table `rbac_auth_item` */

INSERT  INTO `rbac_auth_item`(`name`,`type`,`description`,`rule_name`,`data`,`created_at`,`updated_at`) VALUES ('administrator',1,'Administrator',NULL,NULL,1461762156,1468905312),('createStaff',2,NULL,NULL,NULL,1464847324,1464847324),('customer',1,'Customer',NULL,NULL,1464080094,1468905375),('deleteCustomerProfile',2,NULL,NULL,NULL,1464941085,1464941085),('deleteOwnerProfile',2,NULL,NULL,NULL,1464941104,1464941104),('deleteStaffProfile',2,NULL,NULL,NULL,1464941121,1464941121),('deleteTeacherProfile',2,NULL,NULL,NULL,1464941065,1464941065),('editOwnModel',2,NULL,'ownModelRule',NULL,1461762156,1461762156),('guest',1,'Guest',NULL,NULL,1473259685,1473259685),('loginToBackend',2,NULL,NULL,NULL,1461762156,1461762156),('owner',1,'Owner',NULL,NULL,1464080179,1468905390),('staffmember',1,'Staff Member',NULL,NULL,1464676525,1468905275),('teacher',1,'Teacher',NULL,NULL,1464080152,1468905402),('updateCustomerProfile',2,NULL,NULL,NULL,1464937751,1464937751),('updateOwnerProfile',2,NULL,NULL,NULL,1464937774,1464937774),('updateOwnProfile',2,NULL,'updateOwnProfileRule',NULL,1464856167,1464862673),('updateStaffProfile',2,NULL,NULL,NULL,1464937926,1464937926),('updateTeacherProfile',2,NULL,NULL,NULL,1464934569,1464934569);

/*Data for the table `rbac_auth_item_child` */

INSERT  INTO `rbac_auth_item_child`(`parent`,`child`) VALUES ('owner','createStaff'),('staffmember','deleteCustomerProfile'),('administrator','deleteOwnerProfile'),('owner','deleteStaffProfile'),('staffmember','deleteTeacherProfile'),('staffmember','loginToBackend'),('administrator','owner'),('administrator','staffmember'),('owner','staffmember'),('staffmember','updateCustomerProfile'),('administrator','updateOwnerProfile'),('administrator','updateOwnProfile'),('owner','updateOwnProfile'),('staffmember','updateOwnProfile'),('owner','updateStaffProfile'),('staffmember','updateTeacherProfile');

/*Data for the table `rbac_auth_rule` */

INSERT  INTO `rbac_auth_rule`(`name`,`data`,`created_at`,`updated_at`) VALUES ('ownModelRule','O:29:\"common\\rbac\\rule\\OwnModelRule\":3:{s:4:\"name\";s:12:\"ownModelRule\";s:9:\"createdAt\";i:1461762156;s:9:\"updatedAt\";i:1461762156;}',1461762156,1461762156),('updateOwnProfileRule','O:37:\"common\\rbac\\rule\\UpdateOwnProfileRule\":3:{s:4:\"name\";s:20:\"updateOwnProfileRule\";s:9:\"createdAt\";i:1464866392;s:9:\"updatedAt\";i:1464866392;}',1464866392,1464866392);

SET FOREIGN_KEY_CHECKS = 1;