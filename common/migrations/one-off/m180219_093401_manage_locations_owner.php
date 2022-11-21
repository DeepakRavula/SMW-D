<?php

use yii\db\Migration;
use common\models\Location;
use common\models\User;

/**
 * Class m180219_093401_manage_locations_owner
 */
class m180219_093401_manage_locations_owner extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $locations = Location::find()->all();
        foreach ($locations as $location) {
            $auth = Yii::$app->authManager;
            $child = $auth->getChildrenWithLocation(User::ROLE_OWNER, $location->id);
            if (empty($child['manageLocations'])) {
                $addManageLocation = $auth->getPermission('manageLocations');
                $owner = $auth->getRole(User::ROLE_OWNER);
                $auth->addChildWithLocation($owner, $addManageLocation, $location->id);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180219_093401_manage_locations_owner cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180219_093401_manage_locations_owner cannot be reverted.\n";

        return false;
    }
    */
}
