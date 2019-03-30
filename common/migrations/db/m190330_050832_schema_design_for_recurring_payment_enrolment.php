<?php

use yii\db\Migration;

/**
 * Class m190330_050832_schema_design_for_recurring_payment_enrolment
 */
class m190330_050832_schema_design_for_recurring_payment_enrolment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableSchema = Yii::$app->db->schema->getTableSchema('customer_recurring_payment_enrolment');
        if ($tableSchema == null) {
            $this->createTable('customer_recurring_payment_enrolment', [
                'id' => $this->primaryKey(),
                'enrolmentId' => $this->integer()->notNull(),
                'customerRecurringPaymentId' => $this->integer()->notNull(),
                'createdOn' => $this->timeStamp()->defaultValue(null),
                'updatedOn' => $this->timeStamp()->defaultValue(null),
		        'createdByUserId' =>  $this->integer()->notNull(),
                'updatedByUserId' =>  $this->integer()->notNull(),
            ]);
        }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190330_050832_schema_design_for_recurring_payment_enrolment cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190330_050832_schema_design_for_recurring_payment_enrolment cannot be reverted.\n";

        return false;
    }
    */
}
