SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Add field to `Lesson`
-- ----------------------------
BEGIN;
ALTER TABLE `lesson` ADD COLUMN `classroomId` INT NULL AFTER `teacherId`;
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
