SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

DELETE   im, lo, lh, ll FROM location l
LEFT JOIN item im ON im.`locationId`= l.`id`
LEFT JOIN log lo ON lo.`locationId`= l.`id`
LEFT JOIN log_history lh ON lh.`logId`= lo.`id`
LEFT JOIN log_link ll ON ll.`logId`= lo.`id`
where l.id = :locationToWipe;

DELETE   pfi, pfii, pfli, n4, pfil FROM proforma_invoice pfi
LEFT JOIN note n4 ON n4.`instanceId`= pfi.`id` AND n4.`instanceType` = 5
LEFT JOIN proforma_line_item pfli ON pfli.`proformaInvoiceId`= pfi.`id`
LEFT JOIN proforma_item_invoice pfii ON pfii.`proformaLineItemId`= pfli.`id`
LEFT JOIN proforma_item_lesson pfil ON pfil.`proformaLineItemId`= pfli.`id`
where pfi.locationId = :locationToWipe;

DELETE   cl, cu, tr FROM classroom cl
LEFT JOIN classroom_unavailability cu ON cu.`classroomId`= cl.`id`
LEFT JOIN teacher_room tr ON tr.`classroomId`= cl.`id`
where cl.locationId = :locationToWipe;

DELETE   ul, u, up, ut, uc, uph, ue, ua, cd, py, n1, n2, cu3, cu4, t3, cpp, rbacaa, s, er, q, tu, tad, crs, cua, crp, crpe  FROM user_location ul
LEFT JOIN user u ON u.`id`= ul.`user_id`
LEFT JOIN customer_account cua on cua.`customerId` = ul.`user_id`
LEFT JOIN customer_recurring_payment crp on crp.`customerId` = ul.`user_id`
LEFT JOIN customer_recurring_payment_enrolment crpe on crpe.`customerRecurringPaymentId` = crp.`id`
LEFT JOIN note n2 ON n2.`instanceId`= u.`id` AND n2.`instanceType` = 2
LEFT JOIN customer_referral_sources crs ON crs.`userId` = u.`id`
LEFT JOIN payment py ON py.`user_id`= ul.`user_id`
LEFT JOIN credit_usage cu3 ON cu3.`credit_payment_id`= py.`id`
LEFT JOIN credit_usage cu4 ON cu4.`debit_payment_id`= py.`id`
LEFT JOIN transaction t3 ON t3.`id`= py.`transactionId`
LEFT JOIN rbac_auth_assignment rbacaa ON rbacaa.`user_id`= ul.`user_id`
LEFT JOIN user_profile up ON up.`user_id`= ul.`user_id`
LEFT JOIN user_token ut ON ut.`user_id`= ul.`user_id`
LEFT JOIN user_contact uc ON uc.`userId`= ul.`user_id`
LEFT JOIN user_phone uph ON uph.`userContactId`= uc.`id`
LEFT JOIN user_email ue ON ue.`userContactId`= uc.`id`
LEFT JOIN user_address ua ON ua.`userContactId`= uc.`id`
LEFT JOIN customer_discount cd ON cd.`customerId`= u.`id`
LEFT JOIN customer_payment_preference cpp ON cpp.`userId`= u.`id`
LEFT JOIN student s ON s.`customer_id`= u.`id`
LEFT JOIN note n1 ON n1.`instanceId`= s.`id` AND n1.`instanceType` = 1
LEFT JOIN exam_result er ON er.`studentId`= s.`id`
LEFT JOIN qualification q ON q.`teacher_id`= u.`id`
LEFT JOIN teacher_unavailability tu ON tu.`teacherId`= u.`id`
LEFT JOIN teacher_availability_day tad ON tad.`teacher_location_id`= ul.`location_id`
where ul.location_id = :locationToWipe;

DELETE   iv, ip, ili, iil, iipcl, iie, n3, pfpf, t2, ilid, ir FROM invoice iv
LEFT JOIN note n3 ON n3.`instanceId`= iv.`id` AND n3.`instanceType` = 4
LEFT JOIN proforma_payment_frequency pfpf ON pfpf.`invoiceId`= iv.`id`
LEFT JOIN invoice_line_item ili ON ili.`invoice_id`= iv.`id`
LEFT JOIN invoice_item_lesson iil ON iil.`invoiceLineItemId`= ili.`id`
LEFT JOIN invoice_item_enrolment iie ON iie.`invoiceLineItemId`= ili.`id`
LEFT JOIN invoice_item_payment_cycle_lesson iipcl ON iipcl.`invoiceLineItemId`= ili.`id`
LEFT JOIN invoice_line_item_discount ilid ON ilid.`invoiceLineItemId`= ili.`id`
LEFT JOIN invoice_payment ip ON ip.`invoice_id`= iv.`id`
LEFT JOIN invoice_reverse ir ON ir.`invoiceId`= iv.`id`
LEFT JOIN transaction t2 ON t2.`id`= iv.`transactionId`
where iv.location_id = :locationToWipe;

DELETE ce, cg, cpr, cs, e, ed, pc, pcl  FROM course co
LEFT JOIN course_extra ce ON ce.`courseId`= co.`id`
LEFT JOIN course_group cg ON cg.`courseId`= co.`id`
LEFT JOIN course_program_rate cpr ON cpr.`courseId`= co.`id`
LEFT JOIN course_schedule cs ON cs.`courseId`= co.`id`
LEFT JOIN enrolment e ON e.`courseId`= co.`id`
LEFT JOIN enrolment_discount ed ON ed.`enrolmentId`= e.`id`
LEFT JOIN payment_cycle pc ON pc.`enrolmentId`= e.`id`
LEFT JOIN payment_cycle_lesson pcl ON pcl.`paymentCycleId`= pc.`id`
where co.locationId = :locationToWipe;

DELETE brl, lsu, pl  FROM course co
LEFT JOIN lesson le ON le.`courseId`= co.`id`
LEFT JOIN bulk_reschedule_lesson brl ON brl.`lessonId`= le.`id`
LEFT JOIN lesson_split_usage lsu ON lsu.`lessonId`= le.`id`
LEFT JOIN private_lesson pl ON pl.`lessonId`= le.`id`
where co.locationId = :locationToWipe;

DELETE ld  FROM course co
LEFT JOIN lesson le ON le.`courseId`= co.`id`
LEFT JOIN lesson_discount ld ON ld.`lessonId`= le.`id`
where co.locationId = :locationToWipe;

DELETE lp, n5  FROM course co
LEFT JOIN lesson le ON le.`courseId`= co.`id`
LEFT JOIN note n5 ON n5.`instanceId`= le.`id` AND n5.`instanceType` = 3
LEFT JOIN lesson_payment lp ON lp.`lessonId`= le.`id`
where co.locationId = :locationToWipe;

DELETE lh1, lh2  FROM course co
LEFT JOIN lesson le ON le.`courseId`= co.`id`
LEFT JOIN lesson_hierarchy lh1 ON lh1.`lessonId`= le.`id`
LEFT JOIN lesson_hierarchy lh2 ON lh2.`childLessonId`= le.`id`
where co.locationId = :locationToWipe;

DELETE co, le  FROM course co
LEFT JOIN lesson le ON le.`courseId`= co.`id`
where co.locationId = :locationToWipe;

DELETE loc, locav, locdebt, locpp, locwalk from location loc
LEFT JOIN location_availability locav ON loc.`id` = locav.`locationId`
LEFT JOIN location_debt locdebt ON loc.`id` = locdebt.`locationId`
LEFT JOIN location_payment_preference locpp ON loc.`id` = locpp.`locationId`
LEFT JOIN location_walkin_customer locwalk ON loc.`id` = locwalk.`locationId` 
where loc.id = :locationToWipe;

SET FOREIGN_KEY_CHECKS = 1;