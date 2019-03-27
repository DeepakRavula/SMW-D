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
        $columns = [];
        array_push($columns, [
            'headerOptions' => ['style' => 'width:15%;text-align:left'],
            'contentOptions' => ['style' => 'width:15%;text-align:left'],
            'label' => 'Date',
            'value' => function ($data) {
                $date = Yii::$app->formatter->asDate($data->date);
                $lessonTime = (new \DateTime($data->date))->format('H:i:s');
                return !empty($date) ? $date.' @ '.Yii::$app->formatter->asTime($lessonTime) : null;
            }
        ]);
  
        array_push($columns, [
            'headerOptions' => ['style' => 'width:15%;text-align:left'],
            'contentOptions' => ['style' => 'width:15%;text-align:left'],
            'label' => 'Student',
            'value' => function ($data) {
                return !empty($data->course->enrolment->student->fullName) ? $data->course->enrolment->student->fullName : null;
            },
        ]);

        array_push($columns, [
            'headerOptions' => ['style' => 'width:10%;text-align:left'],
            'contentOptions' => ['style' => 'width:10%;text-align:left'],
            'attribute' => 'program',
            'label' => 'Program',
            'value' => function ($model) {
                return $model->course->program->name;
            }
        ]);

        array_push($columns, [
            'headerOptions' => ['style' => 'width:15%;text-align:left'],
            'contentOptions' => ['style' => 'width:15%;text-align:left'],
            'label' => 'Teacher',
            'value' => function ($data) {
                return $data->teacher->publicIdentity;
            }
        ]);

        array_push($columns, [
            'headerOptions' => ['style' => 'width:7%;text-align:right'],
            'contentOptions' => ['style' => 'width:7%;text-align:right'],
            'label' => 'Amount',
            'attribute' => 'amount',
            'value' => function ($data) {
                return Yii::$app->formatter->asCurrency(round($data->privateLesson->total, 2));
            },
        ]);

        array_push($columns, [
            'headerOptions' => ['style' => 'width:7%;text-align:right'],
            'contentOptions' => ['style' => 'width:7%;text-align:right'],
            'attribute' => 'balance',
            'label' => 'Balance',
            'value' => function ($data) {
                return Yii::$app->formatter->asBalance(round($data->privateLesson->balance, 2));
            },
        ]);

      
    ?>


<?php $gridId = 'lesson-line-item-grid-mail'; $pjaxId = 'lesson-line-item-listing-mail'; ?>
<?php Pjax::begin(['enablePushState' => false, 'id' => $pjaxId, 'timeout' => 12000,]); ?>

    <?= GridView::widget([
        'options' => ['id' => $gridId],
        'dataProvider' => $lessonLineItemsDataProvider,
        'columns' => $columns,
        'summary' => false,
        //'rowOptions' => ['class' => 'line-items-value lesson-line-items'],
        'emptyText' => 'No Lessons Available!'
    ]); ?>

<?php Pjax::end(); ?>