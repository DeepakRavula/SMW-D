SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `invoice_discount``
-- ----------------------------
CREATE OR REPLACE VIEW `customer_account_info` AS

SELECT
'Invoice' AS 'description', 
`invoice`.`id` AS 'invoiceId', 
`invoice`.`transactionId` AS 'transactionId', 
`invoice`.`user_id` AS 'userId', 
`invoice`.`createdOn` AS 'date', 
`invoice`.`total` AS 'debit',
'0' AS 'credit'
FROM `invoice`
WHERE (`invoice`.`type` = 2)
UNION

SELECT
'Payment' AS 'description',
`invoice_payment`.invoice_id AS 'invoiceId',
`payment`.`transactionId` AS 'transactionId', 
`payment`.`user_id` AS 'userId', 
`payment`.`date`,
'0' AS 'debit',
`payment`.`amount` * -1 AS 'credit'
FROM `payment` 
LEFT JOIN `invoice_payment` ON `payment`.`id` = `invoice_payment`.`payment_id` 
WHERE NOT (`payment_method_id` IN (7, 2, 3));

SET FOREIGN_KEY_CHECKS = 1;