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
			$form->field($model, 'student_id')->dropDownList(
				ArrayHelper::map(
					Student::find()
					->location($locationId)
					->unenrolled($groupCourseModel->id)
					->notDeleted()
					->all(),
				'id', 'fullName'
				),
			['multiple' => 'multiple','size' => '14'])
			?>
    </div>

    <div class="col-xs-2">
        <button type="button" id="groupenrolment-student_id_undo" class="btn btn-primary btn-block">undo</button>
        <button type="button" id="groupenrolment-student_id_rightAll" class="btn btn-default btn-block"><i class="glyphicon glyphicon-forward"></i></button>
        <button type="button" id="groupenrolment-student_id_rightSelected" class="btn btn-default btn-block"><i class="glyphicon glyphicon-chevron-right"></i></button>
        <button type="button" id="groupenrolment-student_id_leftSelected" class="btn btn-default btn-block"><i class="glyphicon glyphicon-chevron-left"></i></button>
        <button type="button" id="groupenrolment-student_id_leftAll" class="btn btn-default btn-block"><i class="glyphicon glyphicon-backward"></i></button>
        <button type="button" id="groupenrolment-student_id_redo" class="btn btn-warning btn-block">redo</button>
    </div>

    <div class="col-xs-5">
		<?=
		$form->field($model, "studentIds")->dropDownList(
			ArrayHelper::map(
					Student::find()
					->location($locationId)
					->enrolled($groupCourseModel->id)
					->notDeleted()
					->all(),
				'id', 'fullName'
				),
		['multiple' => 'multiple', 'id' => 'groupenrolment-student_id_to','size' => '14'])
		?>
    </div>
    <div class="col-md-12 form-group">
       <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
    </div>
<?php ActiveForm::end(); ?>

</div>