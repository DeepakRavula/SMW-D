<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use common\components\gridView\AdminLteGridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'All Locations';
?>
<div class="form-group form-inline">
<?php echo $this->render('_search', ['model' => $searchModel]); ?>
</div>
<div class="clearfix"></div>
<div class="grid-row-open"> 
    <?php Pjax::begin(['id' => 'locations-listing']); ?>
    <?php
    echo AdminLteGridView::widget([
        'dataProvider' => $dataProvider,
        'rowOptions' => function ($model, $key, $index, $grid) {
            $url = Url::to(['student/view', 'id' => $model->id]);
            $data = ['data-url' => $url];
            return $data;
        },
        'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'columns' => [
        'name',
            [
            'label' => 'Active Students',
            'value' => function ($data) use ($searchModel) {
                return !empty($data->getActiveStudentsCount($searchModel->fromDate, $searchModel->toDate)) ? $data->getActiveStudentsCount($searchModel->fromDate, $searchModel->toDate) : 0;
            },
        ],
            [
            'label' => 'Revenue',
            'value' => function ($data) use ($searchModel) {
                return !empty($data->getRevenue($searchModel->fromDate, $searchModel->toDate)) ? $data->getRevenue($searchModel->fromDate, $searchModel->toDate) : 0;
            },
        ],
            [
            'label' => 'Royalty',
            'value' => function ($data) use ($searchModel) {
                $revenue=$data->getRevenue($searchModel->fromDate, $searchModel->toDate);
                $royaltyValue=$revenue*(($data->royalty->value)/100);
                return !empty($revenue)&& ($revenue > 0) ? round($royaltyValue,2) : 0;
            },
        ],
            [
            'label' => 'Advertisement',
            'value' => function ($data) use ($searchModel) {
                $revenue = $data->getRevenue($searchModel->fromDate, $searchModel->toDate);
                $advertisementValue = $revenue * (($data->advertisement->value) / 100);
                return !empty($revenue) && ($revenue > 0) ? round($advertisementValue, 2) : 0;
            },
        ],
            [
            'label' => 'HST',
            'value' => function ($data) use ($searchModel) {
                $revenue = $data->getRevenue($searchModel->fromDate, $searchModel->toDate);
                $royaltyValue=$revenue*(($data->royalty->value)/100);
                $advertisementValue = $revenue * (($data->advertisement->value) / 100);
                $subTotal=$royaltyValue+$advertisementValue;
                $taxPercentage=$data->getTax();
                $taxAmount=$subTotal * ($taxPercentage / 100);
                return !empty($revenue) && ($revenue > 0) ? round($taxAmount, 2) : 0;
            },
        ],
            [
            'label' => 'Total',
            'value' => function ($data) use ($searchModel) {
                $revenue = $data->getRevenue($searchModel->fromDate, $searchModel->toDate);
                $royaltyValue = $revenue * (($data->royalty->value) / 100);
                $advertisementValue = $revenue * (($data->advertisement->value) / 100);
                $subTotal = $royaltyValue + $advertisementValue;
                $taxPercentage = $data->getTax();
                $taxAmount = $subTotal * ($taxPercentage / 100);
                $total=$subTotal+$taxAmount;
                return !empty($revenue) && ($revenue > 0) ? round($total,2) : 0;
            },
        ],
    ],
]);

    ?>
<?php Pjax::end(); ?>
</div>