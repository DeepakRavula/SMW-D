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
use common\models\Course;
use common\models\CustomerRecurringPaymentEnrolment;

?>

<?php 
    $form = ActiveForm::begin([
        'id' => 'modal-form-enrolment',
        'enableClientValidation' => false
    ]);
?>

    <?php  
        $columns = [];
            array_push($columns, [
                'class' => 'yii\grid\CheckboxColumn',
                'contentOptions' => ['style' => 'width:30px;'],
                'checkboxOptions' => function($model, $key, $index, $column) use($customerRecurringPaymentModel){
                   $enrolments = $customerRecurringPaymentModel->enrolments;
                   $checked = false;
                   foreach ($enrolments as $enrolment) {
                        if ($model->id === $enrolment->id){
                            $checked = true;
                        }
                   }
                    return ['checked' => $checked,'class' =>'check-checkbox'];
                }
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
            'label' => 'Student',
            'attribute' => 'student',
            'value' => function ($data) {
                return $data->student->fullName;
            },
        ]);

        array_push($columns, [
            'attribute' => 'teacher',
            'headerOptions' => ['class' => 'text-left'],
            'label' => 'Teacher',
            'value' => function ($data) {
                return $data->course->teacher->publicIdentity;
            }
        ]);

        array_push($columns, [
            'label' => 'Day',
            'value' => function ($data) {
                $dayList = Course::getWeekdaysList();
                $day = $dayList[$data->courseSchedule->day];
                return !empty($day) ? $day : null;
            }
        ]);

        array_push($columns, [
            'label' => 'From Time',
            'value' => function ($data) {
                return  Yii::$app->formatter->asTime($data->courseSchedule->fromTime);
            }
        ]);
        array_push($columns, [
            'label' => 'Duration',
            'value' => function ($data) {
                $duration = \DateTime::createFromFormat('h:i:s', $data->courseSchedule->duration);
                return  $duration->format('H:i');
            }
        ]);
    ?>
<?php ActiveForm::end(); ?>

<?php Pjax::begin(['enablePushState' => false, 'id' => 'enrolment-listing', 'timeout' => 12000,]); ?>
    <?= GridView::widget([
        'dataProvider' => $enrolmentDataProvider,
        'options' => ['id' => 'enrolment-index'],
        'columns' => $columns,
        'summary' => false,
        'emptyText' => 'No enrolment Available!'
    ]); ?>
<?php Pjax::end(); ?>
<script>


 </script>