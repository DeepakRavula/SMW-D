<?php

use common\components\gridView\KartikGridView;
use yii\widgets\Pjax;
use yii\helpers\Url;
use kartik\grid\GridView;
use common\models\Location;
use yii\helpers\ArrayHelper;
use common\models\Student;
use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;
?>

<?php
$form = ActiveForm::begin([
    'id' => 'modal-form-lesson',
    'enableClientValidation' => false
]);
?>

<?php
$columns = [];
if ($searchModel->showCheckBox) {
    array_push($columns, [
        'class' => 'yii\grid\CheckboxColumn',
        'contentOptions' => ['style' => 'width:30px;'],
        'checkboxOptions' => function ($model, $key, $index, $column) {
            return ['checked' => true, 'class' => 'check-checkbox'];
        }
    ]);
}

array_push($columns, [
    'headerOptions' => ['class' => 'text-left', 'style' => 'width:13%'],
    'contentOptions' => ['class' => 'text-left', 'style' => 'width:13%'],
    'label' => 'Date',
    'value' => function ($data) {
        $date = Yii::$app->formatter->asDate($data->date);
        $lessonTime = (new \DateTime($data->date))->format('H:i:s');
        return !empty($date) ? $date . ' @ ' . Yii::$app->formatter->asTime($lessonTime) : null;
    }
]);

array_push($columns, [
    'headerOptions' => ['class' => 'text-left', 'style' => 'width:17%'],
    'contentOptions' => ['class' => 'text-left', 'style' => 'width:17%'],
    'label' => 'Due Date',
    'attribute' => 'dueDateRange',
    'filterType' => KartikGridView::FILTER_DATE_RANGE,
    'filterWidgetOptions' => [
        'model' => $searchModel,
        'convertFormat' => true,
        'initRangeExpr' => true,
        'attribute' => 'dueDateRange',
        'options' => [
            'readOnly' => true,
        ],
        'pluginOptions' => [
            'autoApply' => true,
            'ranges' => [
                Yii::t('kvdrp', 'This Month') => ["moment().startOf('month')", "moment().endOf('month')"],
                Yii::t('kvdrp', 'Next Month') => ["moment().add(1, 'month').startOf('month')", "moment().add(1, 'month').endOf('month')"],
                Yii::t('kvdrp', 'Next 3 Months') => ["moment().add(1, 'month').startOf('month')", "moment().add(3, 'month').endOf('month')"],
                Yii::t('kvdrp', 'Next 6 Months') => ["moment().add(1, 'month').startOf('month')", "moment().add(6, 'month').endOf('month')"],
                Yii::t('kvdrp', 'Next 12 Months') => ["moment().add(1, 'month').startOf('month')", "moment().add(12, 'month').endOf('month')"],
            ],
            'locale' => [
                'format' => 'M d, Y',
            ],
            'opens' => 'right'
        ]
    ],
    'value' => function ($data) {
        $date = Yii::$app->formatter->asDate($data->dueDate);
        $lessonTime = (new \DateTime($data->dueDate))->format('H:i:s');
        return !empty($date) ? $date : null;
    }
]);

array_push($columns, [
    'label' => 'Student',
    'attribute' => 'student',
    'value' => function ($data) {
        return !empty($data->course->enrolment->student->fullName) ? $data->course->enrolment->student->fullName : null;
    },
    'filterType' => KartikGridView::FILTER_SELECT2,
    'filter' => ArrayHelper::map(Student::find()
        ->notDeleted()
        ->orderBy(['first_name' => SORT_ASC])
        ->joinWith(['enrolments' => function ($query) {
            $query->joinWith(['course' => function ($query) {
                $query->confirmed()
                    ->notDeleted()
                    ->location(Location::findOne(['slug' => \Yii::$app->location])->id);
            }]);
        }])
        ->customer($model->userId)
        ->all(), 'id', 'fullName'),
    'filterWidgetOptions' => [
        'options' => [
            'id' => 'student_filter_lesson_line_item'
        ],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ],
    'filterInputOptions' => ['placeholder' => 'Student'],
]);

array_push($columns, [
    'headerOptions' => ['class' => 'text-left'],
    'contentOptions' => ['class' => 'text-left'],
    'attribute' => 'program',
    'label' => 'Program',
    'value' => function ($model) {
        return $model->course->program->name;
    }
]);

array_push($columns, [
    'attribute' => 'teacher',
    'headerOptions' => ['class' => 'text-left'],
    'label' => 'Teacher',
    'value' => function ($data) {
        return $data->teacher->publicIdentity;
    }
]);

array_push($columns, [
    'label' => 'Amount',
    'attribute' => 'amount',
    'value' => function ($data) {
        return Yii::$app->formatter->asCurrency(round($data->privateLesson->total ?? 0, 2));
    },
    'headerOptions' => ['class' => 'text-right'],
    'contentOptions' => ['class' => 'text-right']
]);

array_push($columns, [
    'attribute' => 'balance',
    'label' => 'Balance',
    'value' => function ($data) {
        return Yii::$app->formatter->asBalance(round($data->privateLesson->balance ?? 0, 2));
    },
    'headerOptions' => ['class' => 'text-right'],
    'contentOptions' => ['class' => 'text-right invoice-value']
]);
if (isset($changeGridId)) {
    array_push($columns, [
        'label' => 'Status',
        'value' => function ($data) {
            return $data->getPaidStatus($data->enrolment->id);
        },
        'headerOptions' => ['class' => 'text-right'],
        'contentOptions' => ['class' => 'text-right invoice-value']
    ]);
}

if ($searchModel->showCheckBox && !$isCreatePfi) {
    array_push($columns, [
        'headerOptions' => ['class' => 'text-right', 'style' => 'width:120px'],
        'contentOptions' => ['class' => 'text-right', 'style' => 'width:120px'],
        'label' => 'Payment',
        'value' => function ($data) use ($form) {
            return $form->field($data, 'paymentAmount')->textInput([
                'value' => round($data->privateLesson->balance ?? 0, 2),
                'class' => 'form-control text-right payment-amount',
                'id' => 'lesson-payment-' . $data->id,
                'lessonId' => $data->id
            ])->label(false);
        },
        'attribute' => 'new_activity',
        'format' => 'raw',
    ]);
}
?>
<?php ActiveForm::end(); ?>

<?php $gridId = 'lesson-line-item-grid';
$pjaxId = 'lesson-line-item-listing'; ?>
<?php if (isset($changeGridId)) {
    $gridId = 'lesson-line-item-grid-pr';
    $pjaxId = 'lesson-line-item-listing-pr';
} ?>

<?php Pjax::begin(['enablePushState' => false, 'id' => $pjaxId, 'timeout' => 12000,]); ?>
<?php if ($searchModel->showCheckBox) : ?>
    <?= GridView::widget([
        'options' => ['id' => $gridId],
        'dataProvider' => $lessonLineItemsDataProvider,
        'filterModel' => $searchModel,
        'filterUrl' => $isCreatePfi ? Url::to(['proforma-invoice/create', 'PaymentFormLessonSearch[userId]' => $searchModel->userId]) :
            Url::to(['payment/receive', 'PaymentFormLessonSearch[userId]' => $searchModel->userId]),
        'columns' => $columns,
        'summary' => false,
        'rowOptions' => ['class' => 'line-items-value lesson-line-items'],
        'emptyText' => 'No Lessons Available!'
    ]); ?>
<?php else : ?>

    <?= GridView::widget([
        'options' => ['id' => $gridId],
        'dataProvider' => $lessonLineItemsDataProvider,
        'columns' => $columns,
        'summary' => false,
        'emptyText' => 'No Lessons Available!'
    ]); ?>
<?php endif; ?>

<?php Pjax::end(); ?>
<script>
    $(document).off('change', '#paymentformlessonsearch-duedaterange').on('change', '#paymentformlessonsearch-duedaterange', function() {
        var duedateRange = $('#paymentformlessonsearch-duedaterange').val();
        var result = duedateRange.split('-');
        var fromDate = result[0].trim();
        var toDate = result[1].trim();
        var checkFromDate =  moment(fromDate).format('Y-m-d');
        var checkToDate = moment(toDate).format('Y-m-d');
        if (checkFromDate === 'Invalid date' || checkToDate === 'Invalid date') {
            $('#paymentformlessonsearch-duedaterange').parent().append('<p class="help-block help-block-error"><div style="color:#dd4b39">Invalid Format</div></p>');
            $('#paymentformlessonsearch-duedaterange').val("");
        }
        return false;
    });
</script>