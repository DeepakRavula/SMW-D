<?php

use yii\db\Migration;
use common\models\User;
use common\models\Location;

/**
 * Class m190523_103201_adding_permission_for_account_receivable
 */
class m190523_103201_adding_permission_for_account_receivable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;
        $admin = $auth->getRole(User::ROLE_ADMINISTRATOR);
        $owner = $auth->getRole(User::ROLE_OWNER);
        $staffMember = $auth->getRole(User::ROLE_STAFFMEMBER);
        $newPermissions = [
            [
                'permission' => 'manageAccountReceivableReport',
                'description' => 'Manage Account Receivable Report'
            ],           
        ];
        $locations = Location::find()->all();
        foreach ($newPermissions as $newPermission) {
            $loginToBackend = $auth->getPermission($newPermission['permission']);
            if (!$loginToBackend) {
                $loginToBackend = $auth->createPermission($newPermission['permission']);
                $loginToBackend->description = $newPermission['description'];
                $loginToBackend->isLocationSpecific = true;
                $auth->add($loginToBackend);
            }
            foreach ($locations as $location) {
                $locationId = $location->id;
                $adminItem = $auth->getChildrenWithLocation(User::ROLE_ADMINISTRATOR, $locationId);
                $ownerItem = $auth->getChildrenWithLocation(User::ROLE_OWNER, $locationId);
                $staffMemberItem = $auth->getChildrenWithLocation(User::ROLE_STAFFMEMBER, $locationId);
                if (empty($adminItem[$newPermission['permission']])) {
                    $auth->addChildWithLocation($admin, $loginToBackend, $locationId);
                }
                if (empty($ownerItem[$newPermission['permission']])) {
                    $auth->addChildWithLocation($owner, $loginToBackend, $locationId);
                }
                if (empty($staffMemberItem[$newPermission['permission']])) {
                    $auth->addChildWithLocation($staffMember, $loginToBackend, $locationId);
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190523_103201_adding_permission_for_account_receivable cannot be reverted.\n";

        return false;
    }
}
