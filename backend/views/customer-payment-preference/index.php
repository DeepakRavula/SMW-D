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
$locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
$columns = [
    [
        'label' => 'Customer',
        'attribute' => 'customer',
        'value' => function ($data) {
            return $data->customer->publicIdentity . " (" . $data->customer->customerPaymentPreference->dayOfMonth . 
                "  of every payment cycle using " . $data->customer->customerPaymentPreference->getPaymentMethodName() . 
                " till " . Yii::$app->formatter->asDate($data->customer->customerPaymentPreference->expiryDate) . ")" ?? null;
        },
        'group' => true,
        'groupedRow' => true,
    ],
    [
        'label' => 'Program',
        'attribute' => 'day',
        'value' => function ($data) {
            return $data->program->name ?? null;
        }
    ],
    [
        'label' => 'Student',
        'attribute' => 'paymentMethod',
        'value' => function ($data) {
            return $data->student->fullName ?? null;
        }
    ],
    [
        'label' => 'Current Payment Cycle',
        'attribute' => 'dateRange',
        'value' => function ($data) {
            $currentDate = new \DateTime();
            $priorDate = $currentDate->modify('+ 15 days')->format('Y-m-d');
            $paymentCycleFormattedDates = [];
            $dateRange = $data->getCurrentPaymentCycleDateRange($priorDate);
            $paymentCycleDates = explode(' - ', $dateRange);
            foreach ($paymentCycleDates as $paymentCycleDate) {
                $paymentCycleDate = Carbon::parse($paymentCycleDate)->format('M d, Y');
                $paymentCycleFormattedDates[] = $paymentCycleDate;
            }
            $dateRange = implode(' - ', $paymentCycleFormattedDates);
            return $dateRange ?? null;
        }
    ],
    [
        'label' => 'Lessons Count',
        'attribute' => 'dateRange',
        'value' => function ($data) {
            $currentDate = new \DateTime();
            $priorDate = $currentDate->modify('+ 15 days')->format('Y-m-d');
            $dateRange = $data->getCurrentPaymentCycleDateRange($priorDate);
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
        }
    ],
    [
        'label' => 'Invoiced Lessons Count',
        'attribute' => 'dateRange',
        'value' => function ($data) {
            $currentDate = new \DateTime();
            $priorDate = $currentDate->modify('+ 15 days')->format('Y-m-d');
            $dateRange = $data->getCurrentPaymentCycleDateRange($priorDate);
            $paymentCycleDates = explode(' - ', $dateRange);
            $fromDate = new \DateTime($paymentCycleDates[0]);
            $toDate = new \DateTime($paymentCycleDates[1]);
            $lessons = Lesson::find()
                ->notDeleted()
                ->isConfirmed()
                ->notCanceled()
                ->between($fromDate, $toDate)
                ->enrolment($data->id)
                ->invoiced()
                ->all();
            return count($lessons);
        }
    ],
    [
        'label' => 'Manualy Paid Lessons Count',
        'attribute' => 'dateRange',
        'value' => function ($data) {
            $currentDate = new \DateTime();
            $priorDate = $currentDate->modify('+ 15 days')->format('Y-m-d');
            $dateRange = $data->getCurrentPaymentCycleDateRange($priorDate);
            $paymentCycleDates = explode(' - ', $dateRange);
            $fromDate = new \DateTime($paymentCycleDates[0]);
            $toDate = new \DateTime($paymentCycleDates[1]);
            $lessons = Lesson::find()
                ->notDeleted()
                ->isConfirmed()
                ->notCanceled()
                ->between($fromDate, $toDate)
                ->enrolment($data->id)
                ->joinWith(['lessonPayments' => function ($query) {
                    $query->joinWith(['payment' => function ($query) {
                        $query->notCreditUsed()
                            ->andWhere(['NOT', ['payment.createdByUserId' => 727]]);
                    }]);
                }])
                ->all();
            return count($lessons);
        }
    ],
    [
        'label' => 'Automaticaly Paid Lessons Count',
        'attribute' => 'dateRange',
        'value' => function ($data) {
            $currentDate = new \DateTime();
            $priorDate = $currentDate->modify('+ 15 days')->format('Y-m-d');
            $dateRange = $data->getCurrentPaymentCycleDateRange($priorDate);
            $paymentCycleDates = explode(' - ', $dateRange);
            $fromDate = new \DateTime($paymentCycleDates[0]);
            $toDate = new \DateTime($paymentCycleDates[1]);
            $lessons = Lesson::find()
                ->notDeleted()
                ->isConfirmed()
                ->notCanceled()
                ->between($fromDate, $toDate)
                ->enrolment($data->id)
                ->joinWith(['lessonPayments' => function ($query) {
                    $query->joinWith(['payment' => function ($query) {
                        $query->notCreditUsed()
                        ->andWhere(['payment.createdByUserId' => 727]);
                    }]);
                }])
                ->all();
            return count($lessons);
        }
    ],
    [
        'label' => 'Unpaid Lessons Count',
        'attribute' => 'dateRange',
        'value' => function ($data) {
            $currentDate = new \DateTime();
            $priorDate = $currentDate->modify('+ 15 days')->format('Y-m-d');
            $dateRange = $data->getCurrentPaymentCycleDateRange($priorDate);
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
            $unInvoicedLessons = Lesson::find()   
                ->notDeleted()
                ->isConfirmed()
                ->notCanceled()
                ->between($fromDate, $toDate)
                ->enrolment($data->id)
                ->joinWith(['privateLesson' => function($query) {
                    $query->andWhere(['>', 'private_lesson.balance', 0]);
                }])
                ->leftJoin(['invoiced_lesson' => $invoicedLessons], 'lesson.id = invoiced_lesson.id')
                ->andWhere(['invoiced_lesson.id' => null])
                ->orderBy(['lesson.date' => SORT_ASC])
                ->all();
            return count($unInvoicedLessons);
        }
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
