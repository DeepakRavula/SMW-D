<?php

use yii\db\Migration;
use common\models\Location;
use common\models\User;

/**
 * Class m200212_074132_add_reports_permission_for_owner
 */
class m200212_074132_add_reports_permission_for_owner extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $location = Location::findOne(['id' => 22]);
        $ownerRole = User::ROLE_OWNER;
        $command = Yii::$app->db->createCommand();
        $command->insert('rbac_auth_item_child', array(
            'parent' => $ownerRole,
            'child' => 'manageAccountReceivableReport',
            'location_id' => $location->id
        ))->execute();
        $command->insert('rbac_auth_item_child', array(
            'parent' => $ownerRole,
            'child' => 'managePaymentsReport',
            'location_id' => $location->id
        ))->execute();
        $command->insert('rbac_auth_item_child', array(
            'parent' => $ownerRole,
            'child' => 'manageSalesAndPayment',
            'location_id' => $location->id
        ))->execute();
        $command->insert('rbac_auth_item_child', array(
            'parent' => $ownerRole,
            'child' => 'manageRecurringPayment',
            'location_id' => $location->id
        ))->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200212_074132_add_reports_permission_for_owner cannot be reverted.\n";

        return false;
    }
}
