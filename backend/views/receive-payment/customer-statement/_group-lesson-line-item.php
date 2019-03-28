<?php

use common\components\gridView\KartikGridView;
use yii\widgets\Pjax;
use yii\helpers\Url;
use kartik\grid\GridView;
use common\models\Location;
use yii\helpers\ArrayHelper;
use common\models\Student;
use common\models\Enrolment;
use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;
?>

    <?php  
        $columns = [];
        array_push($columns, [
            'headerOptions' => ['style' => 'width:15%;text-align:left'],
            'contentOptions' => ['style' => 'width:15%;text-align:left'],
            'label' => 'Date',
            'value' => function ($data) {
                $date = Yii::$app->formatter->asDate($data->lesson->date);
                $lessonTime = (new \DateTime($data->lesson->date))->format('H:i:s');

                return !empty($date) ? $date.' @ '.Yii::$app->formatter->asTime($lessonTime) : null;
            }
        ]);


        array_push($columns, [
            'headerOptions' => ['style' => 'width:15%;text-align:left'],
            'contentOptions' => ['style' => 'width:15%;text-align:left'],
            'label' => 'Student',
            'value' => function ($data) use($searchModel) {
                return $data->enrolment->student->fullName;
            },
        ]);

        array_push($columns, [
            'headerOptions' => ['style' => 'width:10%;text-align:left'],
            'contentOptions' => ['style' => 'width:10%;text-align:left'],
            'label' => 'Program',
            'value' => function ($model) {
                return $model->lesson->course->program->name;
            }
        ]);

        array_push($columns, [
            'headerOptions' => ['style' => 'width:10%;text-align:left'],
            'contentOptions' => ['style' => 'width:10%;text-align:left'],
            'label' => 'Teacher',
            'value' => function ($data) {
                return $data->lesson->teacher->publicIdentity;
            }
        ]);

        array_push($columns, [
            'label' => 'Amount',
            'value' => function ($data) use ($searchModel) {
                return Yii::$app->formatter->asCurrency(round($data->total, 2));
            },
            'headerOptions' => ['style' => 'width:10%;text-align:right'],
            'contentOptions' => ['style' => 'width:10%;text-align:right'],
        ]);

        array_push($columns, [
            'label' => 'Balance',
            'value' => function ($data) use ($searchModel) {
                return Yii::$app->formatter->asCurrency(round($data->total, 2));
            },
            'headerOptions' => ['style' => 'width:10%;text-align:right'],
            'contentOptions' => ['style' => 'width:10%;text-align:right'],
        ]);
    ?>

<?php $gridId = 'group-lesson-line-item-grid-mail'; $pjaxId = 'group-lesson-line-item-listing-mail'; ?>

<?php Pjax::begin(['enablePushState' => false, 'id' => $gridId, 'timeout' => 6000,]); ?>
    <?= GridView::widget([
        'options' => ['id' => $gridId],
        'dataProvider' => $lessonLineItemsDataProvider,
        'columns' => $columns,
        'summary' => false,
        'rowOptions' => ['class' => 'line-items-value group-lesson-line-items'],
        'emptyText' => 'No Lessons Available!'
    ]); ?>

<?php Pjax::end(); ?>
