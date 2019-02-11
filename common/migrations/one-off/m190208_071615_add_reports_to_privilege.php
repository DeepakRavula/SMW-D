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
                'description' => 'Manage reports'
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
        $locations = Location::find()->all();
        foreach ($permissions as $permission) {
            $loginToBackend = $auth->getPermission($permission['permission']);
            if ($loginToBackend) {
                $this->execute("UPDATE rbac_auth_item set isLocationSpecific = true where type = 2 and description = '" .$permission['description']."'");
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
