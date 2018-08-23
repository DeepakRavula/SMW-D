SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

DELETE ip FROM invoice_payment ip
LEFT JOIN invoice iv ON ip.`invoice_id` = iv.`id` WHERE iv.`id` IS NULL;

DELETE lp FROM lesson_payment lp
LEFT JOIN lesson le ON lp.`lessonId` = le.`id` WHERE le.`id` IS NULL;

DELETE lh FROM lesson_hierarchy lh
LEFT JOIN lesson le ON lh.`lessonId` = le.`id` WHERE le.`id` IS NULL;

DELETE lh FROM lesson_hierarchy lh
LEFT JOIN lesson le ON lh.`childLessonId` = le.`id` WHERE le.`id` IS NULL;

DELETE q FROM qualification q
LEFT JOIN user u ON u.`id` = q.`teacher_id` WHERE u.`id` IS NULL;

DELETE tad FROM teacher_availability_day tad
LEFT JOIN user_location ul ON ul.`id` = tad.`teacher_location_id` WHERE ul.`id` IS NULL;

DELETE s FROM student s
LEFT JOIN user u ON u.`id` = s.`customer_id` WHERE u.`id` IS NULL;

DELETE pc, pcl FROM payment_cycle pc
LEFT JOIN payment_cycle_lesson pcl ON pc.`id` = pcl.`paymentCycleId`
LEFT JOIN enrolment e ON pc.`enrolmentId` = e.`id` WHERE e.`id` IS NULL;

DELETE pfli, pfil, pfii FROM proforma_line_item pfli
LEFT JOIN proforma_item_invoice pfii ON pfii.`proformaLineItemId` = pfli.`id`
LEFT JOIN proforma_item_lesson pfil ON pfil.`proformaLineItemId` = pfli.`id`
LEFT JOIN proforma_invoice pfi ON pfi.`id` = pfli.`proformaInvoiceId` WHERE pfi.`id` IS NULL;

DELETE uc, uph, ue, ua FROM user_contact uc
LEFT JOIN user_phone uph ON uph.`userContactId`= uc.`id`
LEFT JOIN user_email ue ON ue.`userContactId`= uc.`id`
LEFT JOIN user_address ua ON ua.`userContactId`= uc.`id`
LEFT JOIN user u ON u.`id` = uc.`userId` WHERE u.`id` IS NULL;

DELETE rbacaa FROM rbac_auth_assignment rbacaa
LEFT JOIN u u ON u.`id`= rbacaa.`user_id` WHERE u.`id` IS NULL;

SET FOREIGN_KEY_CHECKS = 1;