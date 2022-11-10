SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `calendar-event-color`
-- ----------------------------
BEGIN;
DROP TABLE IF EXISTS `calendar-event-color`;
CREATE TABLE `calendar_event_color` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `code` char(8) COLLATE utf8_unicode_ci NOT NULL,
  `cssClass` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

insert into `calendar_event_color` (`id`, `name`, `code`, `cssClass`) values('1','Store Closed','#00ff00','store-closed');
insert into `calendar_event_color` (`id`, `name`, `code`, `cssClass`) values('2',"Teacher's Availability",'#cc0000','teacher-availability');
insert into `calendar_event_color` (`id`, `name`, `code`, `cssClass`) values('3',"Teacher's Unavailability",'#00ffff','teacher-unavailability');
insert into `calendar_event_color` (`id`, `name`, `code`, `cssClass`) values('4','Private Lesson','#3c78d8','private-lesson');
insert into `calendar_event_color` (`id`, `name`, `code`, `cssClass`) values('5','Group Lesson','#cc0000','group-leson');
insert into `calendar_event_color` (`id`, `name`, `code`, `cssClass`) values('6','First Lesson','#674ea7','first-lesson');
insert into `calendar_event_color` (`id`, `name`, `code`, `cssClass`) values('7','Lesson - Original Teacher','#ff00ff','lesson-original-teacher');
insert into `calendar_event_color` (`id`, `name`, `code`, `cssClass`) values('8','Lesson - Assigned Teacher','#0c343d','lesson-assigned-teacher');
insert into `calendar_event_color` (`id`, `name`, `code`, `cssClass`) values('9','Lesson - Original Date','#5b0f00','lesson-original-date');
insert into `calendar_event_color` (`id`, `name`, `code`, `cssClass`) values('10','Lesson - Reschedule Date','#a64d79','lesson-reschedule-date');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
