SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

DELETE    co, ce, cg, cpr, cs, e, ed, pc, py, cu3, cu4, t3, pcl, v, le, lp, lsu, lh1, lh2, pl FROM student_csv scsv1

LEFT JOIN student s1 ON s1.`id`= scsv1.`studentId`
LEFT JOIN user u1 ON u1.`id`= s1.`customer_id`
LEFT JOIN enrolment e ON e.`studentId`= s1.`id`
LEFT JOIN course co ON co.`id`= e.`courseId`
LEFT JOIN course_extra ce ON ce.`courseId`= co.`id`
LEFT JOIN course_group cg ON cg.`courseId`= co.`id`
LEFT JOIN course_program_rate cpr ON cpr.`courseId`= co.`id`
LEFT JOIN course_schedule cs ON cs.`courseId`= co.`id`
LEFT JOIN enrolment_discount ed ON ed.`enrolmentId`= e.`id`
LEFT JOIN payment_cycle pc ON pc.`enrolmentId`= e.`id`
LEFT JOIN payment_cycle_lesson pcl ON pcl.`paymentCycleId`= pc.`id`
LEFT JOIN vacation v ON v.`enrolmentId`= e.`id`
LEFT JOIN lesson le ON le.`courseId`= co.`id`
LEFT JOIN lesson_payment lp ON lp.`lessonId`= le.`id`
LEFT JOIN payment py ON py.`id`= lp.`paymentId`
LEFT JOIN credit_usage cu3 ON cu3.`credit_payment_id`= py.`id`
LEFT JOIN credit_usage cu4 ON cu4.`debit_payment_id`= py.`id`
LEFT JOIN transaction t3 ON t3.`id`= py.`transactionId`
LEFT JOIN lesson_split_usage lsu ON lsu.`lessonId`= le.`id`
LEFT JOIN lesson_hierarchy lh1 ON lh1.`lessonId`= le.`id`
LEFT JOIN lesson_hierarchy lh2 ON lh2.`childLessonId`= le.`id`
LEFT JOIN private_lesson pl ON pl.`lessonId`= le.`id`;

SET FOREIGN_KEY_CHECKS = 1;