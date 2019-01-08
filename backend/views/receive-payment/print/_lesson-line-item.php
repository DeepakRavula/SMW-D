<?php

use common\components\gridView\KartikGridView;
use yii\widgets\Pjax;
use yii\helpers\Url;
use kartik\grid\GridView;
use common\models\Location;
use yii\helpers\ArrayHelper;
use common\models\Student;
use yii\bootstrap\Html;

?>

<?php Pjax::begin(['enablePushState' => false, 'id' => 'lesson-line-item-listing', 'timeout' => 6000]); ?>
    <?php  
        $columns = [];
        array_push($columns, [
            'headerOptions' => ['class' => 'text-left', 'style' => 'width:20%'],
            'contentOptions' => ['class' => 'text-left', 'style' => 'width:20%'],
            'attribute' => 'dateRange',
            'label' => 'Date',
            'value' => function ($data) {
                $date = Yii::$app->formatter->asDate($data->date);
                $lessonTime = (new \DateTime($data->date))->format('H:i:s');

                return !empty($date) ? $date.' @ '.Yii::$app->formatter->asTime($lessonTime) : null;
            }
        ]);

        array_push($columns, [
            'label' => 'Student',
            'attribute' => 'student',
            'value' => function ($data) {
                return !empty($data->course->enrolment->student->fullName) ? $data->course->enrolment->student->fullName : null;
            },
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
                return Yii::$app->formatter->asCurrency(round($data->netPrice, 2));
            },
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right']
        ]);

        array_push($columns, [
            'attribute' => 'payment',
            'label' => 'Payment',
            'value' => function ($data) {
                return Yii::$app->formatter->asCurrency(round($data->getPaidAmount($data->payment->id), 2));
            },
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right invoice-value']
        ]);

        array_push($columns, [
            'attribute' => 'balance',
            'label' => 'Balance',
            'value' => function ($data) {
                return (round($data->getOwingAmount($data->enrolment->id), 2) > 0.00 && 
                    round($data->getOwingAmount($data->enrolment->id), 2) <= 0.09) || 
                    (round($data->getOwingAmount($data->enrolment->id), 2) < 0.00 && 
                    round($data->getOwingAmount($data->enrolment->id), 2) >= -0.09)  ? 
                    Yii::$app->formatter->asCurrency(round('0.00', 2)): 
                    Yii::$app->formatter->asCurrency(round($data->getOwingAmount($data->enrolment->id), 2));
            },
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right invoice-value']
        ]);

    ?>
<?php if ($searchModel->showCheckBox) : ?>
    <?= GridView::widget([
        'options' => ['id' => 'lesson-line-item-grid'],
        'dataProvider' => $lessonLineItemsDataProvider,
        'columns' => $columns,
        'summary' => false,
        'rowOptions' => ['class' => 'line-items-value lesson-line-items'],
        'emptyText' => 'No Lessons Available!'
    ]); ?>
<?php else: ?>
<?= GridView::widget([
        'options' => ['id' => 'lesson-line-item-grid'],
        'dataProvider' => $lessonLineItemsDataProvider,
        'columns' => $columns,
        'summary' => false,
        'emptyText' => 'No Lessons Available!'
    ]); ?>
<?php endif; ?>
<?php Pjax::end(); ?>
