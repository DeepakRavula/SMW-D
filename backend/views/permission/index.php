<?php

use yii\widgets\Pjax;

$this->title = 'Permissions';
?>
<?php Pjax::begin([
    'id' => 'permission-table-pjax',
    'enablePushState' => false,
]) ?>
<?php  $permissions = Yii::$app->authManager->getPermissions();?>
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-body">
                <table class="table table-bordered permission-table">
                    <tbody>
						<tr>
                        <th>Privilege</th>
                        <th style="width:5%">Staff</th>
                        <th style="width:5%">Owner</th>
                        <th style="width:5%">Admin</th>
                    </tr>
					 <?php foreach($permissions as $permission): ?>
                        <tr>
                            <td><?= $permission->name ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody></table>
            </div>
            <!-- /.box-body -->
        </div>
    </div>
</div>
<?php Pjax::end(); ?>