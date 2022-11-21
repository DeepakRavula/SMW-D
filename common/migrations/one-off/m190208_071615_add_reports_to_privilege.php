<?php

use yii\db\Migration;
use common\models\User;
use common\models\Location;

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
                'permission' => 'manageRoyalty',
                'description' => 'Manage royalty report'
            ],
            [
                'permission' => 'manageDiscountReport',
                'description' => 'Manage discount report'
            ],
            [
                'permission' => 'manageRoyaltyFreeItems',
                'description' => 'Manage Royalty free item report'
            ],
            [
                'permission' => 'manageTaxCollected',
                'description' => 'Manage tax collected report'
            ],
        ];
        foreach ($permissions as $permission) {
            $loginToBackend = $auth->getPermission($permission['permission']);
            if ($loginToBackend) {
                $this->execute("UPDATE rbac_auth_item set isLocationSpecific = true where type = 2 and description = '" .$permission['description']."'");
            }
        }
        $admin = $auth->getRole(User::ROLE_ADMINISTRATOR);
        $owner = $auth->getRole(User::ROLE_OWNER);
        $newPermissions = [
            [
                'permission' => 'managePaymentsReport',
                'description' => 'Manage Payment report'
            ],
            [
                'permission' => 'manageSalesAndPayment',
                'description' => 'Manage sales and payment'
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
                if (empty($adminItem[$newPermission['permission']])) {
                    $auth->addChildWithLocation($admin, $loginToBackend, $locationId);
                }
                if (empty($ownerItem[$newPermission['permission']])) {
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
