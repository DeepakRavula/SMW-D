SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `invoice_discount``
-- ----------------------------
CREATE OR REPLACE VIEW `customer_account_info` AS

SELECT
   'Invoice' AS `description`,
   `invoice`.`id` AS `invoiceId`,
   `invoice`.`transactionId` AS `transactionId`,
   `invoice`.`user_id` AS `userId`,
   `invoice`.`createdOn` AS `date`,
    (`invoice`.`total`  * (-1))AS `credit`,'0' AS `debit`
    FROM `invoice` 
    where (`invoice`.`type` = 2) union 
select 'Payment' AS `description`,
    `invoice_payment`.`invoice_id` AS `invoiceId`,
    `payment`.`transactionId` AS `transactionId`,
    `payment`.`user_id` AS `userId`,
    `payment`.`date` AS `date`,
    '0' AS `credit`,
    (`payment`.`amount` * (1)) AS `debit` 
    from (`payment` left join `invoice_payment` on((`payment`.`id` = `invoice_payment`.`payment_id`))) 
    where (`payment`.`payment_method_id` not in (7,2,3));

SET FOREIGN_KEY_CHECKS = 1;