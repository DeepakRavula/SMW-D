<?php

use common\components\gridView\KartikGridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use common\models\User;
use kartik\grid\GridView;
use yii\widgets\ActiveForm;

?>

<?php Pjax::Begin(['id' => 'customer-add-listing', 'timeout' => 6000, 'enablePushState' => false]); ?>
    <?= GridView::widget([
        'options' => ['id' => 'choose-customer'],
        'dataProvider' => $userDataProvider,
        'summary' => false,
        'emptyText' => false,
        'rowOptions' => ['class' => 'add-customer-invoice'],
        'tableOptions' => ['class' => 'table table-condensed'],
        'filterModel' => $searchModel,
        'filterUrl' => Url::to(['invoice/update-customer', 'id' => $model->id, "UserSearch[role_name]" => User::ROLE_CUSTOMER, 
            "UserSearch[showAll]" => true]),
        'columns' => [
            [
                'attribute' => 'firstname',
                'label' => 'First Name',
                'value' => function ($data) {
                    return !empty($data->userProfile->firstname) ? $data->userProfile->firstname : null;
                }
            ],
            [
                'attribute' => 'lastname',
                'label' => 'Last Name',
                'value' => function ($data) {
                    return !empty($data->userProfile->lastname) ? $data->userProfile->lastname : null;
                }
            ],
            'email',
            [
                'attribute' => 'student',
                'label' => 'student',
                'value' => function ($data) {
                    return !empty($data->student) ? $data->getStudentsList() : null;
                }
            ],
        ]
    ]); ?>
<?php Pjax::end(); ?>

<?php $form = ActiveForm::begin([
    'id' => 'modal-form'
]); ?>

    <?= $form->field($model, 'user_id')->hiddenInput()->label(false); ?>
    
<?php ActiveForm::end(); ?>