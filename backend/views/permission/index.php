<?php

use yii\widgets\Pjax;
use common\models\User;
use yii\helpers\ArrayHelper;

$this->registerJsFile('/backend/web/js/permission/index.js', ['depends'=>\yii\web\JqueryAsset::className()]);
$this->title = 'Permissions';
?>
<?php Pjax::begin([
    'id' => 'permission-table-pjax',
    'enablePushState' => false,
]) ?>
<?php  
	$statusTd = function($role, $roles, $permission) {
		$parentPermissions = ArrayHelper::getColumn(Yii::$app->authManager->getChildren($role), 'name');
		if(in_array($role, $roles) && in_array($permission->name,$parentPermissions)) {
			return '<td class="remove-permission" data-role="'.$role.'" data-permission="'.$permission->name.'><i class="fa fa-check"></i></td>';
		} else {
			return '<td class="add-permission" data-role="'.$role.'" data-permission="'.$permission->name.'><i class="fa fa-close"></i></td>';
		}
	};?>
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
							<?=$statusTd(User::ROLE_STAFFMEMBER, $roles, $permission) ?>
                            <?=$statusTd(User::ROLE_OWNER, $roles, $permission) ?>
                            <?=$statusTd(User::ROLE_ADMINISTRATOR, $roles, $permission) ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody></table>
            </div>
            <!-- /.box-body -->
        </div>
    </div>
</div>
<?php Pjax::end(); ?>
<script>
// $(document).ready(function() {
// 	$(document).on('click', '.add-permission', function () {
//		$.ajax({
//			url    : $(this).attr('url'),
//			type   : 'get',
//			success: function(response)
//			{
//				if(response.status)
//					{
//						$('#lesson-payment-modal').modal('show');
//						$('#lesson-payment-content').html(response.data);
//					}
//			}
//		});
//		return false;	
//	});
//	$(document).on('click', '.remove-permission', function () {
//			
//	});
// });
 </script>