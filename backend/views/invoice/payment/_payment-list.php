<?php
use yii\grid\GridView;

$columns = [
        [
       
        'label' => 'Date',
        'value' => function ($data) {
            return Yii::$app->formatter->asDate($data->date);
        },
        ],
    [
        
        'label' => 'Type',
        'value' => function ($data) {
            return $data->paymentMethod->name;
        },
        ],
    [
        
        'label' => 'Ref',
        'value' => function ($data) {
            return $data->reference;
        },
        ],
    [
        'contentOptions' => ['class' => 'text-left'],
        'label' => 'Notes',
        'value' => function ($data) {
            return $data->notes;

        },
        ],
        [
            'label' => 'Amount',
            'format' => 'currency',
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right', 'style' => 'width:15px;'],
            'value' => function ($data) {
                return Yii::$app->formatter->asDecimal($data->amount);
            },
        ],
    ]; ?>
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