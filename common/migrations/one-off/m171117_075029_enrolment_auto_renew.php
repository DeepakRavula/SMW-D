<?php

use yii\db\Migration;

class m171117_075029_enrolment_auto_renew extends Migration
{
    public function up()
    {
        $enrolments = common\models\Enrolment::find()
                ->notDeleted()
                ->isConfirmed()
                ->isRegular()
                ->all();
        foreach ($enrolments as $enrolment) {
            $enrolment->isAutoRenew = true;
            $enrolment->save();
        }
    }

    public function down()
    {
        echo "m171117_075029_enrolment_auto_renew cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
