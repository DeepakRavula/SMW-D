SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Master rows of `city`
-- ----------------------------
BEGIN;
INSERT INTO `city` VALUES ('1', 'Toronto', '1'), ('2', 'Maple', '1'), ('3', 'Woodbridge', '1'), ('4', 'Richmond Hill', '1'), ('5', 'Etobicoke', '1'), ('6', 'Brampton', '1'), ('7', 'Mississauga', '1'), ('8', 'North York', '1'), ('9', 'Vaughan', '1'), ('10', 'Kleinburg', '1'), ('11', 'Woodbriodge', '1'), ('12', 'Woodbride', '1'), ('13', 'Bolton', '1'), ('14', 'Weston', '1'), ('15', 'Concord', '1'), ('16', 'Thornhill', '1'), ('17', 'Vaughan', '1'), ('18', 'Kleinburg', '1'), ('19', 'Downsview', '1'), ('20', 'Nobleton', '1'), ('21', 'Klienburg', '1'), ('22', 'Markham', '1');
COMMIT;

-- ----------------------------
--  Master rows of `city`
-- ----------------------------
BEGIN;
insert  into `country`(`id`,`name`) values (1,'Canada');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;