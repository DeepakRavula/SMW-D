<?php 

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Student;

?>

<?php $form = ActiveForm::begin(); ?>
<?php echo $form->errorSummary($model); ?>
<div class="row p-10">
    <div class="col-xs-5">
			<?php
                $locationId = Yii::$app->session->get('location_id');
            ?>
			<?=
            $form->field($model, 'studentId')->dropDownList(
                ArrayHelper::map(
                    Student::find()
                    ->unenrolled($courseId, $locationId)
                    ->all(),
                'id', function ($model) {
                    return $model->first_name.' '.$model->last_name;
                }
                ),
            ['multiple' => 'multiple', 'size' => '14'])
            ?>
    </div>

    <div class="col-xs-2">
        <button type="button" id="enrolment-studentid_undo" class="btn btn-primary btn-block">undo</button>
        <button type="button" id="enrolment-studentid_rightAll" class="btn btn-default btn-block"><i class="glyphicon glyphicon-forward"></i></button>
        <button type="button" id="enrolment-studentid_rightSelected" class="btn btn-default btn-block"><i class="glyphicon glyphicon-chevron-right"></i></button>
        <button type="button" id="enrolment-studentid_leftSelected" class="btn btn-default btn-block"><i class="glyphicon glyphicon-chevron-left"></i></button>
        <button type="button" id="enrolment-studentid_leftAll" class="btn btn-default btn-block"><i class="glyphicon glyphicon-backward"></i></button>
        <button type="button" id="enrolment-studentid_redo" class="btn btn-warning btn-block">redo</button>
    </div>

    <div class="col-xs-5">
		<?=
        $form->field($model, 'studentIds')->dropDownList(
            ArrayHelper::map(
                    Student::find()
                    ->location($locationId)
                    ->groupCourseEnrolled($courseId)
					->active()
                    ->all(),
                'id', 'fullName'
                ),
        ['multiple' => 'multiple', 'id' => 'enrolment-studentid_to', 'size' => '14'])
        ?>
    </div>
    <div class="col-md-12 form-group">
       <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
    </div>
<?php ActiveForm::end(); ?>

</div>