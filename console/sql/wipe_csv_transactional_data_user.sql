SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

DELETE    u3, s3, scsv3, ul, up, ut, ucu, ucph, ue, ua, cd, cpp, er FROM student_csv scsv3

LEFT JOIN student s3 ON s3.`id`= scsv3.`studentId`
LEFT JOIN user u3 ON u3.`id`= s3.`customer_id`
LEFT JOIN user_location ul ON ul.`user_id`= u3.`id`
LEFT JOIN user_profile up ON up.`user_id`= ul.`user_id`
LEFT JOIN user_token ut ON ut.`user_id`= ul.`user_id`
LEFT JOIN user_contact ucu ON ucu.`userId`= u3.`id`
LEFT JOIN user_phone ucph ON ucph.`userContactId`= ucu.`id`
LEFT JOIN user_email ue ON ue.`userContactId`= ucu.`id`
LEFT JOIN user_address ua ON ua.`userContactId`= ucu.`id`
LEFT JOIN customer_discount cd ON cd.`customerId`= u3.`id`
LEFT JOIN customer_payment_preference cpp ON cpp.`userId`= u3.`id`
LEFT JOIN exam_result er ON er.`studentId`= s3.`id`;

SET FOREIGN_KEY_CHECKS = 1;