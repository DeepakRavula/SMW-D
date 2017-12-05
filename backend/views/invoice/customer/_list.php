<?php

use yii\grid\GridView;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Pjax;
?>
<?php Pjax::Begin(['id' => 'customer-add-listing', 'timeout' => 6000]); ?>
 <?= GridView::widget([
            'dataProvider' => $userDataProvider,
            'summary' =>false,
            'tableOptions' => ['class' => 'table table-condensed'],
            'rowOptions' =>['class' => 'add-customer-invoice'],
            'headerRowOptions' => ['class' => 'bg-light-gray invisible'],
            'filterModel'=>$searchModel,
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