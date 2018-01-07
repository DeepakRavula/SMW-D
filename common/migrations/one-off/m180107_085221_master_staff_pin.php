<?php

use yii\db\Migration;
use common\models\User;
use common\models\StaffDetail;

class m180107_085221_master_staff_pin extends Migration
{
    public function up()
    {
        $users = User::find()
                ->staffs()
                ->all();
        
        foreach ($users as $user) {
            $staffDetails = new StaffDetail();
            $staffDetails->userId = $user->id;
            do {
                $uniqueNumber     = rand(1111, 9999);
                $exists = StaffDetail::findOne(['pin' => $uniqueNumber]);
            } while(!empty($exists));
            $staffDetails->pin = $uniqueNumber;
            $staffDetails->save();
        }
    }

    public function down()
    {
        echo "m180107_085221_master_staff_pin cannot be reverted.\n";

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
