SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Master rows of `city`
-- ----------------------------
BEGIN;
INSERT INTO `city` VALUES ('1', 'Toronto', '1'), ('2', 'Maple', '1'), ('3', 'Woodbridge', '1'), ('4', 'Richmond Hill', '1'), ('5', 'Etobicoke', '1'), ('6', 'Brampton', '1'), ('7', 'Mississauga', '1'), ('8', 'North York', '1'), ('9', 'Vaughan', '1'), ('10', 'Kleinburg', '1'), ('11', 'Woodbriodge', '1'), ('12', 'Woodbride', '1'), ('13', 'Bolton', '1'), ('14', 'Weston', '1'), ('15', 'Concord', '1'), ('16', 'Thornhill', '1'), ('17', 'Vaughan', '1'), ('18', 'Kleinburg', '1'), ('19', 'Downsview', '1'), ('20', 'Nobleton', '1'), ('21', 'Klienburg', '1'), ('22', 'Markham', '1');
COMMIT;

-- ----------------------------
--  Master rows of `country`
-- ----------------------------
BEGIN;
INSERT  INTO `country`(`id`,`name`) VALUES (1,'Canada');
COMMIT;

-- ----------------------------
--  Master rows of `holiday`
-- ----------------------------
BEGIN;
INSERT  INTO `holiday`(`id`,`date`) VALUES (1,'2016-01-01 00:00:00'),(2,'2016-02-15 00:00:00'),(3,'2016-03-25 00:00:00'),(4,'2016-05-23 00:00:00'),(5,'2016-07-01 00:00:00'),(6,'2016-08-01 00:00:00'),(7,'2016-09-05 00:00:00'),(8,'2016-10-10 00:00:00'),(9,'2016-12-26 00:00:00');
COMMIT;

-- ----------------------------
--  Master rows of `item_type`
-- ----------------------------
BEGIN;
INSERT  INTO `item_type`(`id`,`name`) VALUES (1,'Private Lesson'),(2,'Group Lesson'),(3,'Misc'),(4,'Opening Balance');
COMMIT;

-- ----------------------------
--  Master rows of `payment_method`
-- ----------------------------
BEGIN;
INSERT  INTO `payment_method`(`id`,`name`,`active`,`displayed`,`sortOrder`) VALUES (1,'Account Entry',1,0,0),(2,'Credit Used',1,0,0),(3,'Credit Applied',1,0,0),(4,'Cash',1,1,1),(5,'Cheque',1,1,2),(6,'Credit Card',0,0,0),(7,'Apply Credit',1,1,7),(8,'Visa',1,1,4),(9,'Mastercard',1,1,5),(10,'Amex',1,1,6),(11,'Debit',1,1,3);
COMMIT;

-- ----------------------------
--  Master rows of `phone_label`
-- ----------------------------
BEGIN;
INSERT  INTO `phone_label`(`id`,`name`) VALUES (1,'Home'),(2,'Work'),(3,'Other');
COMMIT;

-- ----------------------------
--  Master rows of `professional_development_day`
-- ----------------------------
BEGIN;
INSERT  INTO `professional_development_day`(`id`,`date`) VALUES (1,'2016-01-30 00:00:00'),(2,'2016-02-29 00:00:00'),(3,'2016-03-29 00:00:00'),(4,'2016-03-30 00:00:00'),(5,'2016-03-31 00:00:00'),(6,'2016-04-29 00:00:00'),(7,'2016-04-30 00:00:00'),(8,'2016-05-30 00:00:00'),(9,'2016-05-31 00:00:00'),(10,'2016-06-29 00:00:00'),(11,'2016-06-30 00:00:00'),(12,'2016-07-30 00:00:00'),(13,'2016-08-30 00:00:00'),(14,'2016-08-31 00:00:00'),(15,'2016-09-29 00:00:00'),(16,'2016-09-30 00:00:00'),(17,'2016-10-29 00:00:00'),(18,'2016-10-31 00:00:00'),(19,'2016-10-29 00:00:00'),(20,'2016-11-30 00:00:00'),(21,'2016-12-29 00:00:00'),(22,'2016-12-30 00:00:00'),(23,'2016-12-31 00:00:00');
COMMIT;

-- ----------------------------
--  Master rows of `program`
-- ----------------------------
BEGIN;
INSERT  INTO `program`(`id`,`name`,`rate`,`status`,`type`) VALUES (1,'piano',105,1,1),(2,'violin',75,1,1),(3,'guitar',105,1,1),(4,'flute theory',250,1,2),(5,'drum theory',400,1,2),(6,'trumpet theory',300,1,2),(10,'drum',400,1,1),(11,'cello theory',200,1,2),(12,'test ',300,0,2),(13,'grouptest',200,0,2),(14,'2016 Fall Band Program',350,1,2),(15,'cello',400,1,1),(16,'Basic Rudiments',425,1,2),(17,'Vocal',120,1,1);
COMMIT;

-- ----------------------------
--  Master rows of `province`
-- ----------------------------
BEGIN;
INSERT  INTO `province`(`id`,`name`,`tax_rate`,`country_id`) VALUES (1,'Ontario',13,1);
COMMIT;

-- ----------------------------
--  Master rows of `tax_code`
-- ----------------------------
BEGIN;
INSERT  INTO `tax_code`(`id`,`tax_type_id`,`province_id`,`rate`,`start_date`,`code`) VALUES (1,1,1,'13.00','2016-08-01 15:30:01','ON'),(2,2,1,'5.00','2016-09-01 15:52:13','ON'),(3,3,1,'0.00','2016-10-06 11:19:31','ON');
COMMIT;

-- ----------------------------
--  Master rows of `tax_status`
-- ----------------------------
BEGIN;
INSERT  INTO `tax_status`(`id`,`name`) VALUES (1,'Default'),(2,'No Tax'),(3,'GST Only');
COMMIT;

-- ----------------------------
--  Master rows of `tax_type`
-- ----------------------------
BEGIN;
INSERT  INTO `tax_type`(`id`,`name`,`status`,`sort_order`,`compounded`) VALUES (1,'HST',1,1,1),(2,'GST',1,2,1),(3,'NO TAX',1,3,1);
COMMIT;

-- ----------------------------
--  Master rows of `tax_type_tax_status_assoc`
-- ----------------------------
BEGIN;
INSERT  INTO `tax_type_tax_status_assoc`(`id`,`tax_type_id`,`tax_status_id`,`exempt`) VALUES (1,1,1,1),(2,2,3,1),(3,3,2,0);
COMMIT;

-- ----------------------------
--  Master rows of `location`
-- ----------------------------
BEGIN;
INSERT  INTO `location`(`id`,`name`,`address`,`phone_number`,`city_id`,`province_id`,`postal_code`,`country_id`,`from_time`,`to_time`,`slug`) VALUES (1,'Arcadia Corporate','205 Marycroft Ave., Unit 6, Woodbridge','905-254-3424',1,1,'L4L 5X8',1,'08:00:00','22:15:00','arcadia-corporate'),(2,'Newmarket','1670 Bayview Ave. Unit B102-105, Newmarket','417-254-3425',1,1,'L3X 1W1',1,'09:00:00','22:15:00','newmarket'),(3,'South Brampton','16700 Bayview Ave. Unit B102-105, Newmarket','(905)254-3424',1,1,'L3X 1W1',1,'06:00:00','22:15:00','south-brampton'),(4,'Bolton','12 Parr Blvd., Unit 7 & 8, Bolton','805-254-3424',1,1,'L7E 4H1',1,'07:30:00','22:15:00','bolton'),(5,'North Brampton','9960 McVean Rd., Unit 4, Brampton','705-254-3424',1,1,'L6P 2S5',1,'08:30:00','22:15:00','north-brampton'),(6,'West Brampton','10625 Creditview Rd., Unit 3-C, Brampton','(678) 254-3424',1,1,'L7A 3A4',1,'10:00:00','22:15:00','west-brampton'),(7,'Maple','2620 Rutherford Rd., Unit 5,6,7, Vibrant Square, Maple','905-254-9673',1,1,'L4K 0H1',1,'06:00:00','22:15:00','maple'),(8,'Richmond Hill','10909 Yonge St., Unit 8, Richmond Hill','978-254-3424',1,1,'L4C 3E3',1,'07:00:00','22:15:00','richmond-hill'),(9,'Woodbridge','205 Marycroft Ave., Unit 6, Woodbridge','587-254-3424',1,1,'L4L 5X8',1,'08:00:00','22:15:00','woodbridge');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
