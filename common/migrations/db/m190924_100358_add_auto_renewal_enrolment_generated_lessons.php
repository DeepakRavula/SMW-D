<?php

use yii\db\Migration;

/**
 * Class m190924_100358_add_auto_renewal_enrolment_generated_lessons
 */
class m190924_100358_add_auto_renewal_enrolment_generated_lessons extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableSchema = Yii::$app->db->schema->getTableSchema('auto_renewal');
        if ($tableSchema == null) {
            $this->createTable('auto_renewal', [
                'id' => $this->primaryKey(),
                'enrolmentId' => $this->integer()->notNull(),
                'paymentFrequency' => $this->integer()->notNull(),
                'enrolmentEndDateCurrent' => $this->timeStamp()->notNull(),
                'enrolmentEndDateNew' => $this->timeStamp()->notNull(),
                'lastPaymentCycleStartDate' => $this->timeStamp()->notNull(),
                'lastPaymentCycleEndDate' => $this->timeStamp()->notNull(),
                'createdOn' => $this->timeStamp()->defaultValue(null),
                'createdByUserId' =>  $this->integer()->notNull(),
            ]);
        }

        $autoRenewalLessontableSchema = Yii::$app->db->schema->getTableSchema('auto_renewal_lessons');
        if ( $autoRenewalLessontableSchema == null) {
            $this->createTable('auto_renewal_lessons', [
                'id' => $this->primaryKey(),
                'autoRenewalId' => $this->integer()->notNull(),
                'lessonId' => $this->integer()->notNull(),
                'createdOn' => $this->timeStamp()->defaultValue(null),
                'createdByUserId' =>  $this->integer()->notNull(),
            ]);
        }

        $autoRenewalPaymentCycletableSchema = Yii::$app->db->schema->getTableSchema('auto_renewal_payment_cycle');
        if ($autoRenewalPaymentCycletableSchema == null) {
            $this->createTable('auto_renewal_payment_cycle', [
                'id' => $this->primaryKey(),
                'autoRenewalId' => $this->integer()->notNull(),
                'paymentCycleId' => $this->integer()->notNull(),
                'createdOn' => $this->timeStamp()->defaultValue(null),
                'createdByUserId' =>  $this->integer()->notNull(),
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190924_100358_add_auto_renewal_enrolment_generated_lessons cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190924_100358_add_auto_renewal_enrolment_generated_lessons cannot be reverted.\n";

        return false;
    }
    */
}
