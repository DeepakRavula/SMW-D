<?php

use yii\db\Migration;

/**
 * Class m190319_055535_adding_enrolment_payment_frequency_table
 */
class m190319_055535_adding_enrolment_payment_frequency_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableSchema = Yii::$app->db->schema->getTableSchema('enrolment_payment_frequency');

        if ($tableSchema == null) {
            $this->createTable('enrolment_payment_frequency', [
                'id' => $this->primaryKey(),
                'enrolmentId' => $this->integer()->notNull(),
                'paymentFrequencyId' => $this->integer()->notNull(),
                'paymentCycleStartDate' =>  $this->date()->notNull(),
                'createdOn' => $this->timeStamp()->defaultValue(null),
                'updatedOn' => $this->timeStamp()->defaultValue(null),
		        'createdByUserId' =>  $this->integer()->notNull(),
                'updatedByUserId' =>  $this->integer()->notNull(),
                'isDeleted' => $this->boolean()->notNull(),
            ]);
        }
       

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190319_055535_adding_enrolment_payment_frequency_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190319_055535_adding_enrolment_payment_frequency_table cannot be reverted.\n";

        return false;
    }
    */
}
