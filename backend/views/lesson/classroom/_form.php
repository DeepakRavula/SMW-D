<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\date\DatePicker;
use kartik\time\TimePicker;
use kartik\color\ColorInput;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use common\models\Classroom;
use common\models\User;
use common\models\LocationAvailability;
require_once Yii::$app->basePath . '/web/plugins/fullcalendar-time-picker/modal-popup.php';

/* @var $this yii\web\View */
/* @var $model common\models\Student */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="lesson-qualify">
<?php $form = ActiveForm::begin([
		'id' => 'classroom-form',
		'enableAjaxValidation' => true,
		'enableClientValidation' => false,
		'validationUrl' => Url::to(['lesson/validate-on-update', 'id' => $model->id]),
		'action' => Url::to(['lesson/edit-classroom', 'id' => $model->id]),
		'options' => [
			'class' => 'p-10',
		]
	]); ?>
	   <div class=" col-md-5">
		   <?php $locationId = Yii::$app->session->get('location_id'); ?>
		   <?=
		   $form->field($model, 'classroomId')->widget(Select2::classname(), [
			   'data' => ArrayHelper::map(Classroom::find()->orderBy(['name' => SORT_ASC])
					   ->andWhere(['locationId' => $locationId])->all(), 'id', 'name'),
			   'pluginOptions' => [
				   'placeholder' => 'Select Classroom',
				   'allowClear' => true
			   ]
		   ]);
		   ?>
		</div>
        <div class="col-md-5">
        <?php echo $form->field($model, 'colorCode')->widget(ColorInput::classname(), [
                'options' => [
                    'placeholder' => 'Select color ...',
                    'value' => $model->getColorCode(),
                ],
        ]);
        ?>
        </div>
		<div class="clearfix"></div>
   <div class="form-group">
        <?= Html::submitButton(Yii::t('backend', 'Save'), ['id' => 'lesson-edit-save', 'class' => 'btn btn-primary', 'name' => 'button']) ?>
		<?= Html::a('Cancel', '#', ['class' => 'btn btn-default lesson-detail-cancel']);
        ?>
	</div>
	<?php ActiveForm::end(); ?>
</div>