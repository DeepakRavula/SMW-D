<?php 

use yii\grid\GridView;
use yii\widgets\Pjax;

?>

<?php
    $columns = [
        [
            'contentOptions' => ['class' => 'text-left','style' => 'min-width:15%;max-width:15%;'],
            'headerOptions' => ['class' => 'text-left','style' => 'min-width:15%;max-width:15%;'],
            'label' => 'Date',
            'options'=>['width'=>'15%;'] , 
            'value' => function ($data) {
                return Yii::$app->formatter->asDate($data->payment->date);
            }
        ],
        [
            'contentOptions' => ['class' => 'text-left','style' => 'min-width:15%;max-width:15%;'],
            'headerOptions' => ['class' => 'text-left','style' => 'min-width:15%;max-width:15%;'],
            'options' => ['width'=>'15%;'] ,
            'label' => 'Type',
            'value' => function ($data) {
                return $data->payment->paymentMethod->name;
            }
        ],
        [
      
            'label' => 'Ref',
            'options'=>['width'=>'20%;'] ,
            'contentOptions' => ['class' => 'text-left', 'style' => 'min-width:20%;max-width:20%;word-wrap:break-word;'],
            'value' => function ($data) {
                return $data->payment->reference;
            }
        ]
    ];

    if (!$searchModel->isMail) {
        array_push($columns, [
            'contentOptions' => ['class' => 'text-left','style' => 'min-width:30%;max-width:30%;word-wrap:break-word;'],
            'label' => 'Notes',
            'options' => ['width'=>'30%;'],
            'value' => function ($data) {
                return $data->payment->notes;

            },
        ]);
    }

    array_push($columns, [
        'label' => 'Amount',
        'format' => 'currency',
        'options' => ['class' => 'text-right','width'=>'10%;'],
        'headerOptions' => ['class' => 'text-right'],
        'contentOptions' => ['class' => 'text-right'],
        'value' => function ($data) {
            return round($data->amount, 2);
        }
    ]); ?>
    
    <?php if ($searchModel->isWeb) {
        $tableOption = ['class' => 'table table-condensed'];
        $id = 'payment-grid';
    } else {
        $tableOption = ['class' => 'table table-condensed m-0', 'style'=>'width:100%; text-align:left'];
        $id = 'payment-grid-email';
    } ?> 

    <?php Pjax::begin([
        'id' => 'invoice-payment-listing',
        'timeout' => 6000,
    ]) ?>

	<?= GridView::widget([
        'id' => $id,
        'dataProvider' => $invoicePaymentsDataProvider,
        'columns' => $columns,
        'summary' => false,
        'emptyText' => false,
        'options' => ['class' => 'col-md-12'],
        'tableOptions' => $tableOption,
        'headerRowOptions' => ['class' => 'bg-light-gray'],
    ]); ?>
    
<?php Pjax::end(); ?>

