<?php

use common\models\User;
use common\models\Invoice;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
use common\components\gridView\AdminLteGridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$roles = ArrayHelper::getColumn(Yii::$app->authManager->getRoles(), 'description');
foreach ($roles as $name => $description) {
    if ($name === $searchModel->role_name) {
        $role = $description;
        break;
    }
}
$roleName = $searchModel->role_name;
$originalInvoice = Invoice::TYPE_INVOICE;
$this->title = Yii::t('backend',  !isset($role) ? 'User' : $role.'s');
$this->params['action-button'] = Html::a(Yii::t('backend', '<i class="fa fa-plus" aria-hidden="true"></i> Add'), ['create', 'User[role_name]' => $searchModel->role_name], ['class' => 'btn btn-primary btn-sm']);
$this->params['show-all'] = $this->render('_button', [
	'searchModel' => $searchModel
]);
?>
<div class="user-index"> 
    <?php Pjax::begin([
		'id' => 'user-index',
		'timeout' => 6000
	]); ?>
<div class="grid-row-open">
        <?= AdminLteGridView::widget([
            'dataProvider' => $dataProvider,
            'rowOptions' => function ($model, $key, $index, $grid) use ($searchModel, $roleName, $originalInvoice) {
                $url = Url::to(['user/view', 'UserSearch[role_name]' => $roleName, 'id' => $model->id]);
            	$data = ['data-url' => $url];
				if ($searchModel->showAllCustomers || $searchModel->showAllTeachers) {
					if ((int)$model->status === User::STATUS_NOT_ACTIVE) {
						$data = array_merge($data, ['class' => 'danger inactive']);
					} elseif ((int)$model->status === User::STATUS_ACTIVE) {
						$data = array_merge($data, ['class' => 'info active']);
					}
            }

            return $data;
            },
            'tableOptions' => ['class' => 'table table-bordered'],
            'headerRowOptions' => ['class' => 'bg-light-gray'],
			'filterModel' => $searchModel,
            'columns' => [
            [
                'attribute' => 'firstname',
                'label' => 'First Name',
                'value' => function ($data) {
                    return !empty($data->userProfile->firstname) ? $data->userProfile->firstname : null;
                },
            ],
            [
                'attribute' => 'lastname',
                'label' => 'Last Name',
                'value' => function ($data) {
                    return !empty($data->userProfile->lastname) ? $data->userProfile->lastname : null;
                },
            ],
            'email',
            [
                'label' => 'Phone',
                'value' => function ($data) {
                    return !empty($data->phoneNumber->number) ? $data->phoneNumber->number : null;
                },
            ],
        ],
    ]); ?>
</div>
<?php Pjax::end(); ?>
<div class=" m-b-20">
<?php if ($searchModel->role_name === User::ROLE_CUSTOMER && YII_ENV !== 'prod'):?>
	<?php echo Html::a(Yii::t('backend', 'Delete All Customers', [
        'modelClass' => 'User',
        ]),
        ['customer/delete-all'],
        ['class' => 'btn pull-left'])
    ?>
<?php endif; ?>
<?php if ($searchModel->role_name === User::ROLE_STAFFMEMBER && YII_ENV !== 'prod'):?>
	<?php echo Html::a(Yii::t('backend', 'Delete All Staff Members', [
        'modelClass' => 'User',
        ]),
        ['delete-all-staff-members'],
        ['class' => 'btn pull-left delete_staff_all_button'])
    ?>
<?php endif; ?>    
<div class="clearfix"></div>
</div>
</div>
<script>
$(document).ready(function(){
  $("#usersearch-showallcustomers").on("change", function() {
      var showAllCustomers = $(this).is(":checked");
      var url = "<?php echo Url::to(['user/index', 'UserSearch[role_name]' => User::ROLE_CUSTOMER]); ?>&UserSearch[query]=" + "<?php echo $searchModel->query; ?>&UserSearch[showAllCustomers]=" + (showAllCustomers | 0);
      $.pjax.reload({url:url,container:"#user-index",replace:false,  timeout: 6000});  //Reload GridView
    });
	 $("#usersearch-showallteachers").on("change", function() {
      var showAllTeachers = $(this).is(":checked");
      var url = "<?php echo Url::to(['user/index', 'UserSearch[role_name]' => User::ROLE_TEACHER]); ?>&UserSearch[query]=" + "<?php echo $searchModel->query; ?>&UserSearch[showAllTeachers]=" + (showAllTeachers | 0);
      $.pjax.reload({url:url,container:"#user-index",replace:false,  timeout: 6000});  //Reload GridView
    });
});
</script>
