<?php

use yii\db\Migration;
use common\models\User;
use common\models\UserPin;

class m180107_085221_master_staff_pin extends Migration
{
    public function up()
    {
        $users = User::find()
                ->staffs()
                ->all();
        
        foreach ($users as $user) {
            $staffDetails = new UserPin();
            $staffDetails->userId = $user->id;
            do {
                $uniqueNumber     = rand(1111, 9999);
                $exists = UserPin::findOne(['pin' => $uniqueNumber]);
            } while (!empty($exists));
            $staffDetails->pin = $uniqueNumber;
            $staffDetails->save();
        }
        
        $auth = Yii::$app->authManager;
        $admin = $auth->getRole(User::ROLE_ADMINISTRATOR);
        $owner = $auth->getRole(User::ROLE_OWNER);
        
        $loginToBackend = $auth->createPermission('listStaffPin');
        $loginToBackend->description = 'Can view list of staff pin';
        $auth->add($loginToBackend);
        $auth->addChild($admin, $loginToBackend);
        $auth->addChild($owner, $loginToBackend);
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
