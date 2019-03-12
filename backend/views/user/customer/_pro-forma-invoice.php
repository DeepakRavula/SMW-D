<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use common\models\Invoice;

?>

<div class="col-md-12">
    <?php
        echo Html::a(
            Html::tag('i', '', ['class' => 'fa fa-plus-circle']),
            Url::to(['/invoice/create', 'Invoice[customer_id]' => $userModel->id]),
    [
            'class' => 'add-new-invoice pull-right',
            'title' => 'Add'
            ]
);
    ?>
</div>
<div class="grid-row-open">
<?php yii\widgets\Pjax::begin([
    'timeout' => 6000,
]) ?>
<?php echo GridView::widget([
    'dataProvider' => $proFormaInvoiceDataProvider,
    'summary' => false,
    'emptyText' => false,
    'options' => ['class' => 'col-md-12'],
    'tableOptions' => ['class' => 'table table-bordered'],
    'headerRowOptions' => ['class' => 'bg-light-gray'],
    'rowOptions' => function ($model, $key, $index, $grid) {
        $url = Url::to(['invoice/view', 'id' => $model->id]);

        return ['data-url' => $url];
    },
    'columns' => [
        [
            'label' => 'Student Name',
            'value' => function ($data) {
                return $data->getStudentProgramName();
            },
        ],
        [
        'label' => 'Date',
            'value' => function ($data) {
                return !empty($data->date) ? Yii::$app->formatter->asDate($data->date) : null;
            },
        ],
        [
            'label' => 'Status',
            'value' => function ($data) {
                return $data->getStatus();
            },
        ],
        [
            'label' => 'Payment Frequency',
            'value' => function ($data) {
                return !empty($data->proformaPaymentFrequency) ?
                    $data->proformaPaymentFrequency->paymentFrequency->name : null;
            },
        ],
        [
            'attribute' => 'total',
            'label' => 'Total',
            'format' => 'currency',
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right'],
            'enableSorting' => false,
            'value' => function ($data) {
                return round($data->total, 2);
            },
        ],
    ],
]); ?>
<?php \yii\widgets\Pjax::end(); ?>
<div class="clearfix"></div>
</div>
<script>
    $('.add-new-invoice').click(function(){
        $(this).hide();
    });
</script>
