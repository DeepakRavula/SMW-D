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
            'filterModel' => $searchModel,
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
					return Html::a('Add', $url, ['class' => 'add-customer-invoice','id' => $userModel->id ]);
				},
			]
        ],        
        ],
    ]); ?>
<?php Pjax::end(); ?>
