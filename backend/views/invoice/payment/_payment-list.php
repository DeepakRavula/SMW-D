<?php use yii\grid\GridView;
?>
<?php
$columns = [
            [
        'contentOptions' => ['class' => 'text-left','style' => 'min-width:15%;max-width:15%;'],
        'headerOptions' => ['class' => 'text-left','style' => 'min-width:15%;max-width:15%;'],
        'label' => 'Date',
        'options'=>['width'=>'15%;'] , 
        'value' => function ($data) {
            return Yii::$app->formatter->asDate($data->date);
        },
        ],
    [
        'contentOptions' => ['class' => 'text-left','style' => 'min-width:15%;max-width:15%;'],
        'headerOptions' => ['class' => 'text-left','style' => 'min-width:15%;max-width:15%;'],
        'options' => ['width'=>'15%;'] ,
        'label' => 'Type',
        'value' => function ($data) {
            return $data->paymentMethod->name;
        },
        ],
    [
      
         'label' => 'Ref',
         'options'=>['width'=>'20%;'] ,
        'contentOptions' => ['class' => 'text-left', 'style' => 'min-width:20%;max-width:20%;word-wrap:break-word;'],
        'value' => function ($data) {
            return $data->reference;
        },
        ]
];
if (!$searchModel->isMail) {
    array_push($columns, [
        'contentOptions' => ['class' => 'text-left','style' => 'min-width:30%;max-width:30%;word-wrap:break-word;'],
        'label' => 'Notes',
        'options' => ['width'=>'30%;'],
        'value' => function ($data) {
            return $data->notes;

        },
    ]);
}
    array_push($columns, [
        'label'=>'Amount',
        'format' => 'currency',
         'options'=>['class' => 'text-right','width'=>'10%;'] , 
        'headerOptions' => ['class' => 'text-right'],
       'contentOptions' => ['class' => 'text-right'],
        'value' => function ($data) {
            return Yii::$app->formatter->asDecimal($data->amount);
        }
    ]); ?>

<div>
    <?php if ($searchModel->isWeb ) {
        $tableOption = ['class' => 'table table-condensed'];
    } else {
        $tableOption = ['class' => 'table table-condensed m-0', 'style'=>'width:100%; text-align:left'];
    }
    yii\widgets\Pjax::begin([
        'id' => 'invoice-payment-listing',
        'timeout' => 6000,
    ]) ?>
	<?= GridView::widget([
        'id' => 'payment-grid',
        'dataProvider' => $invoicePaymentsDataProvider,
        'columns' => $columns,
    'summary' => false,
        'emptyText' => false,
        'options' => ['class' => 'col-md-12'],
    'tableOptions' => $tableOption,
    'headerRowOptions' => ['class' => 'bg-light-gray'],
    ]);
    ?>
<?php \yii\widgets\Pjax::end(); ?>	
</div>