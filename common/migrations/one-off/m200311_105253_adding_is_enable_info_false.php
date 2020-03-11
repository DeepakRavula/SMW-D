<?php

use common\models\Enrolment;
use yii\db\Migration;

/**
 * Class m200311_105253_adding_is_enable_info_false
 */
class m200311_105253_adding_is_enable_info_false extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $enrolments = Enrolment::find()->all();
        foreach ($enrolments as $enrolment) {
            $enrolment->updateAttributes(['isEnableInfo' => false]);
        }        

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200311_105253_adding_is_enable_info_false cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200311_105253_adding_is_enable_info_false cannot be reverted.\n";

        return false;
    }
    */
}
