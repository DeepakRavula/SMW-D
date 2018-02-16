<?php

use yii\db\Migration;
use common\models\User;

/**
 * Class m180215_194021_alter_manage_location_Permission
 */
class m180215_194021_alter_manage_location_Permission extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;
        $owner = $auth->getRole(User::ROLE_OWNER);
        $manageLocations = $auth->getPermission('manageLocations');
        $auth->addChild($owner, $manageLocations);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180215_194021_alter_manage_location_Permission cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180215_194021_alter_manage_location_Permission cannot be reverted.\n";

        return false;
    }
    */
}
