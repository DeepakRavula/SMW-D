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
    <h3><strong>All Locations </strong></h3>
    <?php if ($searchModel->fromDate === $searchModel->toDate): ?>
    <h3><?=  (new \DateTime($searchModel->toDate))->format('F jS, Y'); ?></h3>
    <?php else: ?>
    <h3><?=  (new \DateTime($searchModel->fromDate))->format('F jS, Y'); ?> to <?=  (new \DateTime($searchModel->toDate))->format('F jS, Y') ?></h3>
    <?php endif; ?></div>
<?= KartikGridView::widget([
        'dataProvider' => $dataProvider,
        'rowOptions' => function ($model, $key, $index, $grid) {
            $url = Url::to(['student/view', 'id' => $model->id]);
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
                'label' => 'Name',
                'value' => function ($data) {
                    return  $data->name;
                },
            ],
            [
            'label' => 'Active Students',
            'value' => function ($data) use ($searchModel) {
                return !empty($data->getActiveStudentsCount($searchModel->fromDate, $searchModel->toDate)) ? $data->getActiveStudentsCount($searchModel->fromDate, $searchModel->toDate) : 0;
            },
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right'],
            'hAlign' => 'right',
            'pageSummary' => true,
            'pageSummaryFunc' => GridView::F_SUM
        ],
            [
            'label' => 'Revenue',
            'format' => 'currency',
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right'],
            'value' => function ($data) use ($searchModel) {
                return !empty($data->getRevenue($searchModel->fromDate, $searchModel->toDate)) ? round($data->getRevenue($searchModel->fromDate, $searchModel->toDate), 2) : 0;
            },
            'hAlign' => 'right',
            'pageSummary' => true,
            'pageSummaryFunc' => GridView::F_SUM
        ],
            [
            'label' => 'Royalty',
            'format' => 'currency',
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right'],
            'value' => function ($data) use ($searchModel) {
                $royaltyValue = $data->getLocationDebt(LocationDebt::TYPE_ROYALTY, $searchModel->fromDate, $searchModel->toDate);
                return round($royaltyValue, 2);
            },
            'hAlign' => 'right',
            'pageSummary' => true,
            'pageSummaryFunc' => GridView::F_SUM
        ],
            [
            'label' => 'Advertisement',
            'format' => 'currency',
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right'],
            'value' => function ($data) use ($searchModel) {
                $advertisementValue = $data->getLocationDebt(LocationDebt::TYPE_ADVERTISEMENT, $searchModel->fromDate, $searchModel->toDate);
                return round($advertisementValue, 2);
            },
            'hAlign' => 'right',
            'pageSummary' => true,
            'pageSummaryFunc' => GridView::F_SUM
        ],
            [
            'label' => 'HST',
            'format' => 'currency',
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right'],
            'value' => function ($data) use ($searchModel) {
                $taxAmount = $data->getTaxAmount($searchModel->fromDate, $searchModel->toDate);
                return round($taxAmount, 2);
            },
            'hAlign' => 'right',
            'pageSummary' => true,
            'pageSummaryFunc' => GridView::F_SUM
        ],
            [
            'label' => 'Total',
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right'],
            'format' => 'currency',
            'value' => function ($data) use ($searchModel) {
                $subTotal = $data->SubTotal($searchModel->fromDate, $searchModel->toDate);
                $taxAmount = $data->getTaxAmount($searchModel->fromDate, $searchModel->toDate);
                $total = $subTotal + $taxAmount;
                return round($total, 2);
            },
            'hAlign' => 'right',
            'pageSummary' => true,
            'pageSummaryFunc' => GridView::F_SUM
        ],
    ],
]);

    ?>
</div>

 <script>
    $(document).ready(function () {
        setTimeout(function(){
            window.print();
}, 1500)
    });
</script>