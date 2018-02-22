<?php use yii\grid\GridView;
?>
<?php
$columns = [
      [
        //'contentOptions' => ['class' => 'text-left','style' => 'min-width:40px;max-width:40px;'],
        //'headerOptions' => ['class' => 'text-left','style' => 'min-width:40px;max-width:40px;'],
        'label' => 'Date',
        'options'=>['width'=>'90px;'] , 
        'value' => function ($data) {
            return Yii::$app->formatter->asDate($data->date);
        },
        ],
    [
       // 'contentOptions' => ['class' => 'text-left','style' => 'min-width:50px;max-width:50px;'],
        //'headerOptions' => ['class' => 'text-left','style' => 'min-width:50px;max-width:50px;'],
        'options'=>['width'=>'90px;'] , 
        'label' => 'Type',
        'value' => function ($data) {
            return $data->paymentMethod->name;
        },
        ],
    [
      
         'label' => 'Ref',
         'options'=>['width'=>'90px;'] , 
        'contentOptions' => ['class' => 'text-left', 'style' => 'min-width:90px;max-width:90px;word-wrap:break-word;'],
        'value' => function ($data) {
            return $data->reference;
        },
        ],
    [
        'contentOptions' => ['class' => 'text-left','style' => 'min-width:250px;max-width:250px;word-wrap:break-word;'],
        'label' => 'Notes',
        'options'=>['width'=>'250px;',] , 
        'value' => function ($data) {
            return $data->notes;

        },
        ],
        [
            'label'=>'Amount',
            'format' => 'currency',
             'options'=>['class' => 'text-right','width'=>'90px;'] , 
            'headerOptions' => ['class' => 'text-right'],
           'contentOptions' => ['class' => 'text-right'],
            'value' => function ($data) {
                return Yii::$app->formatter->asDecimal($data->amount);
            },
        ],
    ]; ?>

<div>
	<?php yii\widgets\Pjax::begin([
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
    'tableOptions' => ['class' => 'table table-condensed'],
    'headerRowOptions' => ['class' => 'bg-light-gray'],
    ]);
    ?>
<?php \yii\widgets\Pjax::end(); ?>	
</div>