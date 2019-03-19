<?php

use yii\db\Migration;
use common\models\Enrolment;

/**
 * Class m190319_060311_adding_enrolment_payment_frequency_table_date
 */
class m190319_060311_adding_enrolment_payment_frequency_table_date extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $enrolments = Enrolment::find()
                    ->notDeleted()
                    ->isConfirmed()
                    ->isRegular()
                    ->private()
                    ->all();
        foreach ($enrolments as $enrolment) {

        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190319_060311_adding_enrolment_payment_frequency_table_date cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190319_060311_adding_enrolment_payment_frequency_table_date cannot be reverted.\n";

        return false;
    }
    */
}
