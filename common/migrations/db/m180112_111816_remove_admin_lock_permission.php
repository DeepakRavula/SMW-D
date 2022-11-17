<?php

use common\models\User;
use yii\db\Migration;

class m180112_111816_remove_admin_lock_permission extends Migration
{
    public function up()
    {
        $auth = Yii::$app->authManager;
        $admin = $auth->getRole(User::ROLE_ADMINISTRATOR);
        $owner = $auth->getRole(User::ROLE_OWNER);
        $loginToBackend = $auth->getPermission('listStaffPin');
        $auth->removeChild($admin, $loginToBackend);
        $auth->removeChild($owner, $loginToBackend);
        $auth->remove($loginToBackend);
    }

    public function down()
    {
        echo "m180112_111816_remove_admin_lock_permission cannot be reverted.\n";

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
