<?php

use yii\helpers\Html;
use common\models\User;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use kartik\select2\Select2Asset;
use yii\bootstrap\ButtonGroup;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\grid\GridView;

Select2Asset::register($this);

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */
/* @var $form yii\bootstrap\ActiveForm */
?>
    <?php $form = ActiveForm::begin([
        'method' => 'post',
        'id' => 'customer-form',
		'action' => Url::to(['invoice/update-customer', 'id' => $model->id])
    ]); ?>

    <?php 
	$locationId = Yii::$app->session->get('location_id'); 
	$customers = ArrayHelper::map(User::find()
        ->join('INNER JOIN', 'user_location', 'user_location.user_id = user.id')
         ->join('LEFT JOIN', 'user_profile','user_profile.user_id = user_location.user_id')
        ->join('INNER JOIN', 'rbac_auth_assignment', 'rbac_auth_assignment.user_id = user.id')
        ->where(['user_location.location_id' => $locationId, 'rbac_auth_assignment.item_name' => 'customer'])
        ->notDeleted()
        ->orderBy('user_profile.firstname')
        ->all(), 'id', 'userProfile.fullName');
    ?>
		<?=
			 $form->field($model, "user_id")->widget(Select2::classname(), [
                                    'data' => $customers,
                                    'options' => ['placeholder' => 'Select customer'],
                                    'pluginOptions' => [
                                        'tags' => true,
                                        'allowClear' => true,
                                    ],
                            ])->label('Search');
                            ?>
    <?php ActiveForm::end(); ?>
	<?php Pjax::Begin(['id' => 'invoice-view-customer-add-listing', 'timeout' => 6000]); ?>
    <?= GridView::widget([
            'dataProvider' => $userDataProvider,
            'summary' =>false,
            'id'=>'invoice-view-user-gridview',
            'tableOptions' => ['class' => 'table table-condensed'],
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
            	[
			'class' => 'yii\grid\ActionColumn',
			'contentOptions' => ['style' => 'width:50px'],
			'template' => '{view}',
			'buttons' => [
				'view' => function ($url, $userModel) use($model) {
					$url = Url::to(['invoice/update-customer', 'id' => $model->id]);
					return Html::a('Add', $url, ['class' => 'add-customer-in-invoice','id' => $userModel->id ]);
				},
			]
        ],        
        ],
    ]); ?>
<?php Pjax::end(); ?>