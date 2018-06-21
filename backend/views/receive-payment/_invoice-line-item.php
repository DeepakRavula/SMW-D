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
            return ['checked' => true];
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
	    'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right'],
            'label' => 'Amount',
            'value' => function ($data) {
                return !empty($data->total) ? Yii::$app->formatter->asCurrency($data->total) : null;;
            },
		'contentOptions' => ['class' => 'text-right', 'style' => 'width:300px'],
        ]);
        array_push($columns,[
	     'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right'],
            'label' => 'Balance',
            'value' => function ($data) {
                return Yii::$app->formatter->asCurrency($data->balance);
            },
		'contentOptions' => ['class' => 'text-right', 'style' => 'width:300px'],
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
    ]); ?>
<?php Pjax::end(); ?>

