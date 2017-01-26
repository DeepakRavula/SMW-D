<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\TeacherAvailability;
use wbraganca\selectivity\SelectivityWidget;
use common\models\Classroom;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use common\models\TeacherRoom;

/* @var $this yii\web\View */
/* @var $model common\models\PaymentFrequencyDiscount */
/* @var $form yii\bootstrap\ActiveForm */
?>
<div class="calendar-event-color-form">
	<div class="p-10">
    <?php
		$form = ActiveForm::begin([
		'action' => Url::to(['teacher-room/create', 'id' => $userModel->id])
	]); ?>
	<div class="form-group col-lg-6">
		<strong>Day</strong>
	</div>
	<div class="form-group col-lg-6">
		<strong>Classroom</strong>
	</div>
	<?php foreach ($teachersAvailabilities as $index => $teachersAvailability): ?>
	<?php
		$dayList = TeacherAvailability::getWeekdaysList();
		$day = $dayList[$teachersAvailability->day];
		$classrooms = TeacherRoom::find()
			->andWhere(['day' => $teachersAvailability->day])
			->andWhere(['NOT IN', 'teacherId', $userModel->id])
			->all();
		$classroomIds = ArrayHelper::getColumn($classrooms, 'classroomId');

		$allocatedRoom = TeacherRoom::findOne(['teacherId' => $userModel->id, 'day' => $teachersAvailability->day]);
		// necessary for update action.
		if (!$model->isNewRecord) {
			echo Html::activeHiddenInput($model, "[{$index}]id");
		}
	?>
	<?php echo $form->field($model, "[{$index}]teacherId")->hiddenInput(['value' => $teachersAvailability->teacher->id])->label(false); ?>
	<div class="form-group col-lg-6">
	<?php echo $form->field($model, "[{$index}]day")->textInput(['readonly' => true, 'value' => $day])->label(false); ?>
	</div>
	<div class="form-group col-lg-6">
		<?=
                $form->field($model, "[{$index}]classroomId")->widget(SelectivityWidget::classname(), [
                    'pluginOptions' => [
                        'allowClear' => true,
                        'multiple' => false,
                        'items' => ArrayHelper::map(Classroom::find()->andWhere(['NOT IN', 'id', $classroomIds])->all(), 'id', 'name'),
                        'value' => !empty($allocatedRoom->classroomId) ? (string) $allocatedRoom->classroomId : null,
                        'placeholder' => 'Select Classroom',
                    ],
                ])->label(false);
                ?>
	</div>
	<?php endforeach; ?>
	<div class="form-group col-md-12 p-l-20">
       <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    </div>

</div>