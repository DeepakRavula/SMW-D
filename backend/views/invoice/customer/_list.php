<?php

use yii\grid\GridView;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Pjax;
?>
<div id="customer-spinner" class="spinner" style="display:none">
    <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
    <span class="sr-only">Loading...</span>
</div>  
<?php Pjax::Begin(['id' => 'customer-add-listing', 'timeout' => 6000]); ?>
 <?= GridView::widget([
            'dataProvider' => $userDataProvider,
            'summary' =>false,
            'rowOptions'=>['class' => 'add-customer-invoice'],
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
        ],
    ]); ?>
<?php Pjax::end(); ?>
