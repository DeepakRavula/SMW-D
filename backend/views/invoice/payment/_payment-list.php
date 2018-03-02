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
        ],
    [
        'contentOptions' => ['class' => 'text-left','style' => 'min-width:30%;max-width:30%;word-wrap:break-word;'],
        'label' => 'Notes',
        'options' => ['width'=>'30%;'],
        'value' => function ($data) {
            return $data->notes;

        },
        ],
        [
            'label'=>'Amount',
            'format' => 'currency',
             'options'=>['class' => 'text-right','width'=>'10%;'] , 
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