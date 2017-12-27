<?php

use common\models\User;
use common\models\Invoice;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use common\components\gridView\AdminLteGridView;
use backend\models\UserForm;
use common\models\UserEmail;
use kartik\select2\Select2Asset;

Select2Asset::register($this);

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
$this->params['action-button'] = Html::a(Yii::t('backend', '<i class="fa fa-plus f-s-18 m-l-10" aria-hidden="true"></i>'), ['#'], ['class' => 'f-s-18 add-user']);
$this->params['show-all'] = $this->render('_button', [
	'searchModel' => $searchModel
]);
?>
 <?php
    Modal::begin([
        'header' => '<h4 class="m-0">' . $role . 's / Add</h4>',
        'id'=>'add-user-modal',
    ]);?>
<?= $this->render('_form', [
	'model' => new UserForm(),
	'emailModels' => new UserEmail(),
    'searchModel' => $searchModel,
]);?>
<?php Modal::end();?>
<div class="user-index"> 
<div class="grid-row-open">
    <?php Pjax::begin([
		'id' => 'user-index',
		'timeout' => 6000
	]); ?>
        <?= AdminLteGridView::widget([
            'dataProvider' => $dataProvider,
            'summary' => false,
            'emptyText' => false,
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
<?php Pjax::end(); ?>
</div>
</div>
<script>
$(document).ready(function(){
    $.fn.modal.Constructor.prototype.enforceFocus = function() {};
	$(document).on('click', '.add-user', function() {
        $('#add-user-modal .modal-dialog').css({'width': '400px'});
		$('#add-user-modal').modal('show');
		return false;
	});
	$(document).on('click', '.user-add-cancel', function() {
		$('#add-user-modal').modal('hide');
		return false;
	});
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
