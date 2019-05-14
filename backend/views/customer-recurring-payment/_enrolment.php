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
use Carbon\Carbon;

?>
<?php  $class = ""; 
if (!$customerRecurringPaymentModel->customerId) {
    $class = "multiselect-disable";
}
?>
<?= '<div class = '.$class.'>'; ?>
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
                   $enrolments = ArrayHelper::getColumn($customerRecurringPaymentModel->enrolments, 'id');
                    return ['checked' => in_array($model->id, $enrolments),'class' =>'check-checkbox', 'dueAmount' => $model->dueLessonsInEnrolment(Carbon::now()->format('Y-m-d'))];
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
            'label' => 'Payment Frequency',
            'value' => function ($model) {
                return $model->paymentsFrequency->name;
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
    ?>
<?php ActiveForm::end(); ?>

<?php Pjax::begin(['enablePushState' => false, 'id' => 'enrolment-listing', 'timeout' => 12000,]); ?>
    <?= GridView::widget([
        'dataProvider' => $enrolmentDataProvider,
        'options' => ['id' => 'enrolment-index'],
        'columns' => $columns,
        'summary' => false,
        'rowOptions' => ['class' => 'enrolment-items'],
        'emptyText' => 'No enrolment Available!'
    ]); ?>
<?php Pjax::end(); ?>
    </div>
<script>


 </script>