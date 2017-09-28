<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use common\components\gridView\AdminLteGridView;
use common\models\LocationDebt;

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
                $royaltyValue=$data->getLocationDebt(LocationDebt::TYPE_ROYALTY,$searchModel->fromDate,$searchModel->toDate);
                return round($royaltyValue, 2);
            },
        ],
            [
            'label' => 'Advertisement',
            'value' => function ($data) use ($searchModel) {
                $advertisementValue=$data->getLocationDebt(LocationDebt::TYPE_ADVERTISEMENT,$searchModel->fromDate,$searchModel->toDate);
                return round($advertisementValue, 2);
            },
        ],
            [
            'label' => 'HST',
            'value' => function ($data) use ($searchModel) {
                $taxAmount=$data->getTaxAmount($searchModel->fromDate,$searchModel->toDate);
                return round($taxAmount, 2);
            },
        ],
            [
            'label' => 'Total',
            'value' => function ($data) use ($searchModel) {
                $subTotal=$data->SubTotal($searchModel->fromDate,$searchModel->toDate);
                $taxAmount=$data->getTaxAmount($searchModel->fromDate,$searchModel->toDate);
                $total=$subTotal+$taxAmount;
                return round($total,2);
            },
        ],
    ],
]);

    ?>
<?php Pjax::end(); ?>
</div>
