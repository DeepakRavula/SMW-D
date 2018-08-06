<?php

use backend\assets\CustomGridAsset;
use Carbon\Carbon;
use common\components\gridView\KartikGridView;
use common\models\Location;
use common\models\Lesson;
use yii\helpers\Url;
use yii\widgets\Pjax;

CustomGridAsset::register($this);
Yii::$app->assetManager->bundles['kartik\grid\GridGroupAsset'] = false;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Payment Preferences';
?>
<?php Pjax::begin(['id' => 'payment-preference-listing']);?>

<?php
set_time_limit(0);
ini_set('memory_limit', '-1');
$locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
$columns = [
    [
        'label' => 'Customer',
        'attribute' => 'customer',
        'value' => function ($data) {
            return $data->customer->publicIdentity . " (" . $data->customer->customerPaymentPreference->dayOfMonth . "  of every payment cycle using " . $data->customer->customerPaymentPreference->getPaymentMethodName() . " till " . Yii::$app->formatter->asDate($data->customer->customerPaymentPreference->expiryDate) . ")" ?? null;
        },
        'contentOptions' => ['class' => 'text-left', 'style' => 'width:25%'],
        'headerOptions' => ['class' => 'text-left', 'style' => 'width:25%'],
        'contentOptions' => ['style' => 'font-weight:bold;font-size:14px;text-align:left', 'class' => 'main-group'],
        'group' => true,
        'groupedRow' => true,
    ],
    [
        'label' => 'Program',
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
        'label' => 'Current Payment Cycle Date Range',
        'attribute' => 'dateRange',
        'value' => function ($data) {
            $currentDate = new \DateTime();
            $priorDate = $currentDate->modify('+ 15 days')->format('Y-m-d');
            $paymentCycleFormattedDates = [];
            $dateRange = $data->getCurrentPaymentCycleDateRange(null, $priorDate);
            $paymentCycleDates = explode(' - ', $dateRange);
            foreach ($paymentCycleDates as $paymentCycleDate) {
                $paymentCycleDate = Carbon::parse($paymentCycleDate)->format('M d, Y');
                $paymentCycleFormattedDates[] = $paymentCycleDate;
            }
            $dateRange = implode(' - ', $paymentCycleFormattedDates);
            return !(empty($data->currentPaymentCycle)) ? $dateRange : null;
        },
        'contentOptions' => ['class' => 'text-left', 'style' => 'width:20%'],
        'headerOptions' => ['class' => 'text-left', 'style' => 'width:20%'],
    ],
    [
        'label' => 'Lessons Count of Current Payment Cycle',
        'attribute' => 'dateRange',
        'value' => function ($data) {
            $currentDate = new \DateTime();
            $priorDate = $currentDate->modify('+ 15 days')->format('Y-m-d');
            $dateRange = $data->getCurrentPaymentCycleDateRange(null, $priorDate);
            $paymentCycleDates = explode(' - ', $dateRange);
            $fromDate = new \DateTime($paymentCycleDates[0]);
            $toDate = new \DateTime($paymentCycleDates[1]);
            $lessons = Lesson::find()
                ->notDeleted()
                ->isConfirmed()
                ->notCanceled()
                ->between($fromDate, $toDate)
                ->enrolment($data->id)
                ->all();
            return count($lessons);
        },
        'contentOptions' => ['class' => 'text-left', 'style' => 'width:15%'],
        'headerOptions' => ['class' => 'text-left', 'style' => 'width:15%'],
    ],
    [
        'label' => 'Unpaid Lessons Count of Current Payment Cycle',
        'attribute' => 'dateRange',
        'value' => function ($data) {
            $currentDate = new \DateTime();
            $priorDate = $currentDate->modify('+ 15 days')->format('Y-m-d');
            $dateRange = $data->getCurrentPaymentCycleDateRange(null, $priorDate);
            $paymentCycleDates = explode(' - ', $dateRange);
            $fromDate = new \DateTime($paymentCycleDates[0]);
            $toDate = new \DateTime($paymentCycleDates[1]);
            $invoicedLessons = Lesson::find()
                ->notDeleted()
                ->isConfirmed()
                ->notCanceled()
                ->between($fromDate, $toDate)
                ->enrolment($data->id)
                ->invoiced();
            $query = Lesson::find()   
                ->notDeleted()
                ->isConfirmed()
                ->notCanceled()
                ->between($fromDate, $toDate)
                ->enrolment($data->id)
                ->leftJoin(['invoiced_lesson' => $invoicedLessons], 'lesson.id = invoiced_lesson.id')
                ->andWhere(['invoiced_lesson.id' => null])
                ->orderBy(['lesson.date' => SORT_ASC]);
            $unInvoicedLessons = $query->all();
            $owingLessonIds = [];
            foreach ($unInvoicedLessons as $lesson) {
                if ($lesson->isOwing($data->id)) {
                    $owingLessonIds[] = $lesson->id;
                }
            }
            $lessonsToPay = Lesson::find()
                ->andWhere(['id' => $owingLessonIds])
                ->all();
            return count($lessonsToPay);
        },
        'contentOptions' => ['class' => 'text-left', 'style' => 'width:15%'],
        'headerOptions' => ['class' => 'text-left', 'style' => 'width:15%'],
    ]
];
?>

<div>
    <?=KartikGridView::widget([
    'dataProvider' => $dataProvider,
    'options' => ['class' => ''],
    'headerRowOptions' => ['class' => 'bg-light-gray'],
    'tableOptions' => ['class' => 'table table-bordered table-responsive table-condensed', 'id' => 'payment'],
    'columns' => $columns,
]);?>
</div>
<?php Pjax::end();?>
