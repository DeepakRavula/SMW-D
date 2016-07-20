<?php 

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Student;
?>

<?php $form = ActiveForm::begin(); ?>
<?php echo $form->errorSummary($model); ?>
<div class="row">
    <div class="col-xs-5">
		<div class="col-sm-4">
			<?=
			$form->field($model, "title")->dropDownList(
					ArrayHelper::map(Student::find()->location($locationId)->excluding($id)->all(), 'id', 'first_name'), ['multiple' => 'multiple'])
			?>
		</div>
        
    </div>

    <div class="col-xs-2">
        <button type="button" id="undo_redo_undo" class="btn btn-primary btn-block">undo</button>
        <button type="button" id="groupcourse-title_rightAll" class="btn btn-default btn-block"><i class="glyphicon glyphicon-forward"></i></button>
        <button type="button" id="undo_redo_rightSelected" class="btn btn-default btn-block"><i class="glyphicon glyphicon-chevron-right"></i></button>
        <button type="button" id="undo_redo_leftSelected" class="btn btn-default btn-block"><i class="glyphicon glyphicon-chevron-left"></i></button>
        <button type="button" id="undo_redo_leftAll" class="btn btn-default btn-block"><i class="glyphicon glyphicon-backward"></i></button>
        <button type="button" id="undo_redo_redo" class="btn btn-warning btn-block">redo</button>
    </div>

    <div class="col-xs-5">
        <select name="to[]" id="groupcourse-title_to" class="form-control" size="13" multiple="multiple"></select>
    </div>
</div>
<div class="form-group">
	<?php echo Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>

</div>