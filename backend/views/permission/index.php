<?php

use yii\widgets\Pjax;
use common\models\User;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use common\models\Location;

$this->registerJsFile('/backend/web/js/permission/index.js', ['depends'=>\yii\web\JqueryAsset::className()]);
$this->title = 'Permissions';
?>
<?php Pjax::begin([
    'id' => 'permission-table-pjax',
    'enablePushState' => false,
]) ?>
<?php
$statusTd = function ($role, $roles, $permission) {
    $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
    $parentPermissionNames = ArrayHelper::getColumn(Yii::$app->authManager->getChildren($role), 'name');
    $parentPermissions = ArrayHelper::toArray(Yii::$app->authManager->getChildren($role), 'name', 'location_id', 'name');
    $addPermission = '<td class="add-permission"  data-role="' . $role . '" data-permission="' . $permission->name . '"><i class="fa fa-close"></i></td>';
    if (in_array($permission->name, $parentPermissionNames)) {
        $permissionLocation = $parentPermissions[$permission->name]['location_id'];
        if ($locationId == $permissionLocation && in_array($role, $roles)) {
            return '<td class="remove-permission" data-role="' . $role . '" data-permission="' . $permission->name . '"><i class="fa fa-check"></i></td>';
        } else {
            return $addPermission;
        }
    } else {
        return $addPermission;
    }
}
?>
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-body">
                <table class="table table-bordered permission-table">
                    <tbody>
						<tr>
                        <th>Privilege</th>
                        <th style="width:5%">Staff</th>
                    </tr>
					 <?php foreach ($permissions as $permission): ?>
                        <tr>
                            <td><?= $permission->description ?></td>
                            <?=$statusTd(User::ROLE_STAFFMEMBER, $roles, $permission) ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody></table>
            </div>
            <!-- /.box-body -->
        </div>
    </div>
</div>
<?php Pjax::end(); ?>