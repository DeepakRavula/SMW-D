<?php

use yii\db\Migration;
use common\models\User;
use common\models\Location;

class m171229_140300_add_locationId extends Migration
{
    const LIST_OWNERS  = 'listOwner';
    const LIST_RELEASE_NOTES = 'listReleaseNote';
    const VIEW_DASHBOARD = 'viewDashboard';
    const QUALIFICATION_RATE = 'viewQualificationRate';
    const VIEW_REPORT = 'viewReport';
    const VIEW_ADMIN = 'viewAdmin';
    
    public function up()
    {
        $this->dropForeignKey('rbac_auth_item_child_ibfk_1', 'rbac_auth_item_child');
        $this->dropForeignKey('rbac_auth_item_child_ibfk_2', 'rbac_auth_item_child');
        $this->dropPrimaryKey('parent', 'rbac_auth_item_child');
        $this->addColumn('rbac_auth_item_child', 'location_id', $this->integer());
        $this->update('rbac_auth_item_child', [
            'location_id' => 1
        ]);
        $auth = Yii::$app->authManager;
        $locations = Location::find()->all();
        $location = array_shift($locations);
        $permissions = $auth->getPermissions();
        $roles = [User::ROLE_ADMINISTRATOR, User::ROLE_STAFFMEMBER, User::ROLE_OWNER];
        $exceptStaffs = [User::ROLE_ADMINISTRATOR, User::ROLE_OWNER];
        $exceptStaffPermissions = [self::LIST_OWNERS, self::LIST_RELEASE_NOTES,self::VIEW_DASHBOARD,self::QUALIFICATION_RATE,self::VIEW_REPORT];
        foreach ($locations as $location) {
            foreach ($permissions as $permission) {
                if (in_array($permission->name, $exceptStaffPermissions)) {
                    foreach ($exceptStaffs as $exceptStaff) {
                        $this->insert('rbac_auth_item_child', [
                            'parent' => $exceptStaff,
                            'child' => $permission->name,
                            'location_id' => $location->id
                        ]);
                    }
                } elseif ($permission->name === self::VIEW_ADMIN) {
                    $this->insert('rbac_auth_item_child', [
                        'parent' => User::ROLE_ADMINISTRATOR,
                        'child' => $permission->name,
                        'location_id' => $location->id
                    ]);
                } else {
                    foreach ($roles as $role) {
                        $this->insert('rbac_auth_item_child', [
                            'parent' => $role,
                            'child' => $permission->name,
                            'location_id' => $location->id
                        ]);
                    }
                }
            }
        }
    }

    public function down()
    {
        echo "m171229_140300_add_locationId cannot be reverted.\n";

        return false;
    }
}
