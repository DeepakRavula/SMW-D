<?php
/* @var $this yii\web\View */
/* @var $model common\models\Invoice */
use common\models\Location;
use common\components\gridView\KartikGridView;
use kartik\grid\GridView;
use common\models\LocationDebt;
use yii\helpers\Url;
 ?>
<?php $model = Location::findOne(['id' => Location::findOne(['slug' => \Yii::$app->location])->id]); ?>
<?php
   echo $this->render('/print/_header', [
       'locationModel'=>$model,
]);
   ?>
<div class = "print-report">
<div>
    <h3><strong>Accounts Receivable </strong></h3>
</div>
<?= KartikGridView::widget([
        'dataProvider' => $dataProvider,
        'rowOptions' => function ($model, $key, $index, $grid) {
            $url = Url::to(['customer/view', 'id' => $model->id]);
            $data = ['data-url' => $url];
            return $data;
        },
        'tableOptions' => ['class' => 'table table-condensed table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'summary' => false,
        'emptyText' => false,
        'showPageSummary' => true,
        'columns' => [
            [
                'label' => 'Customer Name',
                'value' => function ($data) {
                    return  $data->userProfile ? $data->userProfile->fullName : null;
                },
            ],
            [
                'label' => 'OutStanding Invoices',
                'value' => function ($data) {
                    return  $data->getInvoiceOwingAmountTotal($data->id) ? Yii::$app->formatter->asDecimal(round($data->getInvoiceOwingAmountTotal($data->id), 2), 2) : '0.00';
                },
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right', 'class' => 'text-right dollar'],
                'hAlign' => 'right',
                'pageSummary' => true,
                'pageSummaryFunc' => GridView::F_SUM
            ],
            [
                'label' => 'Pre-Paid Lessons',
                'value' => function ($data) {
                    return  $data->getPrePaidLessons($data->id) ? Yii::$app->formatter->asDecimal(round($data->getPrePaidLessons($data->id), 2), 2) : '0.00';
                },
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right', 'class' => 'text-right dollar',],
                'hAlign' => 'right',
                'pageSummary' => true,
                'pageSummaryFunc' => GridView::F_SUM
            ],
            [
                'label' => 'Unused Credits',
                'value' => function ($data) {
                    return  $data->getTotalCredits($data->id) ? Yii::$app->formatter->asDecimal(round($data->getTotalCredits($data->id), 2), 2) : '0.00';
                },
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right', 'class' => 'text-right dollar',],
                'hAlign' => 'right',
                'pageSummary' => true,
                'pageSummaryFunc' => GridView::F_SUM
            ],
            [
                'label' => 'Balance',
                'value' => function ($data) {
                    return  Yii::$app->formatter->asDecimal(round($data->getInvoiceOwingAmountTotal($data->id), 2) - (round($data->getPrePaidLessons($data->id), 2) + round($data->getTotalCredits($data->id), 2)), 2);
                },
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right', 'class' => 'text-right dollar'],
                'hAlign' => 'right',
                'pageSummary' => true,
                'pageSummaryFunc' => GridView::F_SUM
            ],
        ]
]); ?>
</div>

 <script>
    $(document).ready(function () {
        window.print();
    });
</script>