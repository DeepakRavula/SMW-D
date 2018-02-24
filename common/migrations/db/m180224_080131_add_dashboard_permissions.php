<?php

use yii\db\Migration;
use common\models\User;
use common\models\Location;

/**
 * Class m180222_080131_add_dashboard_permissions
 */
class m180224_080131_add_dashboard_permissions extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;
        $admin = $auth->getRole(User::ROLE_ADMINISTRATOR);
        $owner = $auth->getRole(User::ROLE_OWNER);
        $permissions = [
            [
                'permission' => 'manageMonthlyRevenue',
                'description' => 'Manage Monthly Revenue'
            ],
            [
                'permission' => 'manageEnrolmentGains',
                'description' => 'Manage Enrolment Gains'
            ],
            [
                'permission' => 'manageEnrolmentLosses',
                'description' => 'Manage Enrolment Losses'
            ],
            [
                'permission' => 'manageInstructionHours',
                'description' => 'Manage Instruction Hours'
            ],
        ];
        $locations = Location::find()->all();
        foreach ($permissions as $permission) {
            $loginToBackend = $auth->getPermission($permission['permission']);
            if (!$loginToBackend) {
                $loginToBackend = $auth->createPermission($permission['permission']);
                $loginToBackend->description = $permission['description'];
                $loginToBackend->isLocationSpecific = true;
                $auth->add($loginToBackend);
            }
            foreach ($locations as $location) {
                $locationId = $location->id;
                $adminItem = $auth->getChildrenWithLocation(User::ROLE_ADMINISTRATOR, $locationId);
                $ownerItem = $auth->getChildrenWithLocation(User::ROLE_OWNER, $locationId);
                if (empty($adminItem[$permission['permission']])) {
                    $auth->addChildWithLocation($admin, $loginToBackend, $locationId);
                }
                if (empty($ownerItem[$permission['permission']])) {
                    $auth->addChildWithLocation($owner, $loginToBackend, $locationId);
                }
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180222_080131_add_dashboard_permissions cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180222_080131_add_dashboard_permissions cannot be reverted.\n";

        return false;
    }
    */
}
