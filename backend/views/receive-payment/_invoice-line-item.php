<?php

use yii\grid\GridView;
use yii\widgets\Pjax;
use common\models\Lesson;
use yii\helpers\Html;

?>
<?php
  if ($searchModel->showCheckBox) {
    $columns = [
        [
        'class' => 'yii\grid\CheckboxColumn',
        'contentOptions' => ['style' => 'width:30px;'],
        'checkboxOptions' => function($model, $key, $index, $column) {
            return ['checked' => true, 'class' =>'check-checkbox'];
        }
        ]
    ];
} else {
    $columns = [];
       
}
    array_push($columns,[
            'headerOptions' => ['class' => 'text-left'],
            'contentOptions' => ['class' => 'text-left'],
            'label' => 'Date',
            'value' => function ($data) {
                return  !empty($data->date) ? Yii::$app->formatter->asDateTime($data->date): null;
            },
	    'contentOptions' => ['style' => 'width:300px'],
        ]);
        array_push($columns,[
            'headerOptions' => ['class' => 'text-left'],
                'contentOptions' => ['class' => 'text-left'],
                'label' => 'Number',
                'value' => function ($data) {
                    return $data->invoiceNumber;
                },
            'contentOptions' => ['style' => 'width:300px'],
            ]);
        array_push($columns,[
	    'headerOptions' => ['class' => 'text-left'],
            'contentOptions' => ['class' => 'text-left'],
            'label' => 'Amount',
            'value' => function ($data) {
                return !empty($data->total) ? Yii::$app->formatter->asCurrency($data->total) : null;
            },
		'contentOptions' => ['style' => 'width:300px'],
        ]);
        array_push($columns,[
	     'headerOptions' => ['class' => 'text-left'],
            'contentOptions' => ['class' => 'text-left'],
            'label' => 'Balance',
            'value' => function ($data) {
                return $data->balance;
            },
		'contentOptions' => ['style' => 'width:300px','class'=>'invoice-value'],
        ]);
?>
<?php Pjax::Begin(['id' => 'invoice-lineitem-listing', 'timeout' => 6000]); ?>
    <?= GridView::widget([
        'id' => 'invoice-line-item-grid',
        'dataProvider' => $invoiceLineItemsDataProvider,
        'columns' => $columns,
        'summary' => false,
        'emptyText' => false,
        'options' => ['class' => 'col-md-12'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'rowOptions' => ['class' => 'line-items-value']
    ]); ?>
<?php Pjax::end(); ?>

