<?php

use common\models\User;
use common\models\Invoice;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$roles = ArrayHelper::getColumn(
             Yii::$app->authManager->getRoles(), 'description'
        );
foreach ($roles as $name => $description) {
    if ($name === $searchModel->role_name) {
        $role = $description;
        break;
    }
}
$roleName = $searchModel->role_name;
$originalInvoice = Invoice::TYPE_INVOICE;
$this->title = Yii::t('backend',  !isset($role) ? 'User' : $role.'s');
$this->params['action-button'] = Html::a(Yii::t('backend', '<i class="fa fa-plus-circle" aria-hidden="true"></i> Add'), ['create', 'User[role_name]' => $searchModel->role_name], ['class' => 'btn btn-primary btn-sm']);
$this->params['breadcrumbs'][] = $this->title;
?>
<?php if ($searchModel->role_name == 'staffmember') {
    ?>
<style>
	.smw-search{
		left: 135px;
	}
</style>
<?php 
}
?>
<?php if ($searchModel->role_name == 'administrator') {
    ?>
<style>
	.smw-search{
		left: 138px;
	}

</style>
<?php 
}
?>
<style>
	.e1Div{
		top: -61px;
		right: 76px;
	}
</style>

<div class="user-index"> 
	<div class="smw-search">
    <i class="fa fa-search m-l-20 m-t-5 pull-left m-r-10 f-s-16"></i>
    <?php
    $form = ActiveForm::begin([
                'action' => ['index'],
                'method' => 'get',
                'options' => ['class' => 'pull-left'],
    ]);
    ?>
    <?=
    $form->field($searchModel, 'query', [
        'inputOptions' => [
            'placeholder' => 'Search ...',
            'class' => 'search-field',
        ],
    ])->input('search')->label(false);
    ?>
    <?php echo $form->field($searchModel, 'role_name')->hiddenInput()->label(false); ?>
    </div>
    <?php if ($searchModel->role_name === User::ROLE_CUSTOMER):?>
	<div class="pull-right  m-r-20">
		<div class="schedule-index">
			<div class="e1Div">
				<?= $form->field($searchModel, 'showAllCustomers')->checkbox(['data-pjax' => true])->label('Show All'); ?>
			</div>
		</div>
    </div>
    <?php endif; ?>
        
    <?php ActiveForm::end(); ?>
<div class="grid-row-open">
    <?php yii\widgets\Pjax::begin(['id' => 'lesson-index']); ?>
        <?php echo GridView::widget([
            'dataProvider' => $dataProvider,
            'rowOptions' => function ($model, $key, $index, $grid) use ($roleName, $originalInvoice) {
                $url = Url::to(['user/view', 'UserSearch[role_name]' => $roleName, 'id' => $model->id]);

                return ['data-url' => $url];
            },
            'tableOptions' => ['class' => 'table table-bordered'],
            'headerRowOptions' => ['class' => 'bg-light-gray'],
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
<?php yii\widgets\Pjax::end(); ?>
</div>
<div class=" m-b-20">
<?php if ($searchModel->role_name === User::ROLE_CUSTOMER && YII_ENV !== 'prod'):?>
	<?php echo Html::a(Yii::t('backend', 'Delete All Customers', [
        'modelClass' => 'User',
        ]),
        ['delete-all-customer'],
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
      $.pjax.reload({url:url,container:"#lesson-index",replace:false,  timeout: 4000});  //Reload GridView
    });
});
</script>
