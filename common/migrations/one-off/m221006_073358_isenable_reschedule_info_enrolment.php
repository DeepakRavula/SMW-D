<?php

use common\models\Enrolment;
use yii\db\Migration;

/**
 * Class m221006_073358_isenable_reschedule_info_enrolment
 */
class m221006_073358_isenable_reschedule_info_enrolment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            'enrolment',
            'isEnableRescheduleInfo',
            $this->boolean()->after('isEnableInfo')
        );
        $enrolments = Enrolment::find()->all();
        foreach ($enrolments as $enrolment) {
            $enrolment->updateAttributes(['isEnableRescheduleInfo' => false]);
        } 
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221006_073358_isenable_reschedule_info_enrolment cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221006_073358_isenable_reschedule_info_enrolment cannot be reverted.\n";

        return false;
    }
    */
}
