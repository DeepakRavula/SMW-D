<?php

use backend\assets\CustomGridAsset;
use Carbon\Carbon;
use common\components\gridView\KartikGridView;
use common\models\Location;
use yii\helpers\Url;
use yii\widgets\Pjax;

CustomGridAsset::register($this);
Yii::$app->assetManager->bundles['kartik\grid\GridGroupAsset'] = false;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Payment Preferences';
?>
<?php Pjax::begin(['id' => 'payment-preference-listing']);?>

<script type='text/javascript' src="<?php echo Url::base(); ?>/js/kv-grid-group.js"></script>
<?php
$locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
$columns = [
    [
        'label' => 'Customer',
        'attribute' => 'customer',
        'value' => function ($data) {
            return $data->customer->publicIdentity . "  (" . $data->customer->customerPaymentPreference->dayOfMonth . "of every payment cycle using " . $data->customer->customerPaymentPreference->getPaymentMethodName() . " till " . Yii::$app->formatter->asDate($data->customer->customerPaymentPreference->expiryDate) . ")" ?? null;
        },
        'contentOptions' => ['class' => 'text-left', 'style' => 'width:25%'],
        'headerOptions' => ['class' => 'text-left', 'style' => 'width:25%'],
        'contentOptions' => ['style' => 'font-weight:bold;font-size:14px;text-align:left', 'class' => 'main-group'],
        'group' => true,
        'groupedRow' => true,
    ],
    [
        'label' => 'Enrolment',
        'attribute' => 'day',
        'value' => function ($data) {
            return $data->program->name ?? null;
        },
        'contentOptions' => ['class' => 'text-left', 'style' => 'width:25%'],
        'headerOptions' => ['class' => 'text-left', 'style' => 'width:25%'],
    ],
    [
        'label' => 'Student',
        'attribute' => 'paymentMethod',
        'value' => function ($data) {
            return $data->student->fullName ?? null;
        },
        'contentOptions' => ['class' => 'text-left', 'style' => 'width:25%'],
        'headerOptions' => ['class' => 'text-left', 'style' => 'width:25%'],
    ],
    [
        'label' => 'Current Payment Cycle',
        'attribute' => 'dateRange',
        'value' => function ($data) {
            $paymentCycleFormattedDates = [];
            $dateRange = $data->getPaymentCycleDateRange(Carbon::parse($data->currentPaymentCycle->startDate), $data->currentPaymentCycle->endDate);
            $paymentCycleDates = explode(' - ', $dateRange);
            foreach ($paymentCycleDates as $paymentCycleDate) {
                $paymentCycleDate = Carbon::parse($paymentCycleDate)->format('M d, Y');
                $paymentCycleFormattedDates[] = $paymentCycleDate;
            }
            $dateRange = implode(' - ', $paymentCycleFormattedDates);
            return !(empty($data->currentPaymentCycle)) ? $dateRange : null;
        },
        'contentOptions' => ['class' => 'text-left', 'style' => 'width:25%'],
        'headerOptions' => ['class' => 'text-left', 'style' => 'width:25%'],
    ],

];
?>

<div>
    <?=KartikGridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'options' => ['class' => ''],
    'headerRowOptions' => ['class' => 'bg-light-gray'],
    'tableOptions' => ['class' => 'table table-bordered table-responsive table-condensed', 'id' => 'payment'],
    'columns' => $columns,
]);?>
</div>
<?php Pjax::end();?>
