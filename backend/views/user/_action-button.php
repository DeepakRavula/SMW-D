<?php
use yii\helpers\Html;
?>
<?php
$loggedInUserRole = $model->getRoleById(Yii::$app->user->id);
$viewedUserRole = $model->getRoleById($model->id);
echo $loggedInUserRole;
echo $viewedUserRole;die;
Html::a('<i title="Delete" class="fa fa-trash"></i>', ['delete', 'id' => $model->id],
		[ 
			'class' => 'm-r-10 btn btn-box-tool user-delete-button', 
]); ?>