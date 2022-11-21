SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

DELETE    iv, ip, ili, iil, iipcl, iie, ilid, ir, p, cu1, cu2, t1, t2 FROM student_csv scsv2

LEFT JOIN student s2 ON s2.`id`= scsv2.`studentId`
LEFT JOIN user u2 ON u2.`id`= s2.`customer_id`
LEFT JOIN invoice iv ON iv.`user_id`= u2.`id`
LEFT JOIN invoice_line_item ili ON ili.`invoice_id`= iv.`id`
LEFT JOIN invoice_item_lesson iil ON iil.`invoiceLineItemId`= ili.`id`
LEFT JOIN invoice_item_enrolment iie ON iie.`invoiceLineItemId`= ili.`id`
LEFT JOIN invoice_item_payment_cycle_lesson iipcl ON iipcl.`invoiceLineItemId`= ili.`id`
LEFT JOIN invoice_line_item_discount ilid ON ilid.`invoiceLineItemId`= ili.`id`
LEFT JOIN invoice_payment ip ON ip.`invoice_id`= iv.`id`
LEFT JOIN invoice_reverse ir ON ir.`invoiceId`= iv.`id`
LEFT JOIN payment p ON p.`id`= ip.`payment_id`
LEFT JOIN credit_usage cu1 ON cu1.`credit_payment_id`= p.`id`
LEFT JOIN credit_usage cu2 ON cu2.`debit_payment_id`= p.`id`
LEFT JOIN transaction t1 ON t1.`id`= iv.`transactionId`
LEFT JOIN transaction t2 ON t2.`id`= p.`transactionId`;

SET FOREIGN_KEY_CHECKS = 1;