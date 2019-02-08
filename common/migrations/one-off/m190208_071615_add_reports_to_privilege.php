<?php

use yii\db\Migration;

/**
 * Class m190208_071615_add_reports_to_privilege
 */
class m190208_071615_add_reports_to_privilege extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;
        $admin = $auth->getRole(User::ROLE_ADMINISTRATOR);
        $owner = $auth->getRole(User::ROLE_OWNER);
        $permissions = [
            [
                'permission' => 'manageReports',
                'description' => 'Manage reports'
            ],
            [
                'permission' => 'manageBirthdays',
                'description' => 'Manage birthdays'
            ],
            [
                'permission' => 'manageItemCategory',
                'description' => 'Manage item category'
            ],
            [
                'permission' => 'manageItemCategoryReport',
                'description' => 'Manage item category Report'
            ], 
            [
                'permission' => 'manageItemReport',
                'description' => 'Manage item report'
            ], 
            [
                'permission' => 'manageItemsByCustomer',
                'description' => 'Manage items by customer report'
            ],
            [
                'permission' => 'manageReports',
                'description' => 'Manage reports'
            ],
            [
                'permission' => 'manageRoyalty',
                'description' => 'Manage reports'
            ],
            [
                'permission' => 'manageRoyaltyFreeItems',
                'description' => 'Manage Royalty free item report'
            ],
            [
                'permission' => 'manageTaxCollected',
                'description' => 'Manage Royalty free item report'
            ],
            [
                'permission' => 'manageTaxCollected',
                'description' => 'Manage Royalty free item report'
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
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190208_071615_add_reports_to_privilege cannot be reverted.\n";

        return false;
    }
}
