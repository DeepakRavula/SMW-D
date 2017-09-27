<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\date\DatePicker;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use common\models\User;
use common\models\LocationAvailability;

/* @var $this yii\web\View */
/* @var $model common\models\Student */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="lesson-qualify">
<?php $form = ActiveForm::begin([
            'id' => 'lesson-edit-form',
            'enableAjaxValidation' => true,
			'enableClientValidation' => false,
            'validationUrl' => Url::to(['lesson/validate-on-update', 'id' => $model->id]),
            'action' => Url::to(['lesson/update', 'id' => $model->id]),
            'options' => [
                'class' => 'p-10',
            ]
        ]); ?>
		<div class="row">
			<?php
            echo $form->field($model, 'duration')->hiddenInput(['value' => (new \DateTime($model->duration))->format('H:i')])->label(false);?>
	   <div class="col-md-4">
        <?php
        // Dependent Dropdown
        echo $form->field($model, 'teacherId')->dropDownList(
            ArrayHelper::map(User::find()
				->teachers($model->course->program->id, Yii::$app->session->get('location_id'))
                ->join('LEFT JOIN', 'user_profile','user_profile.user_id = ul.user_id')
                ->notDeleted()
                ->orderBy(['user_profile.firstname' => SORT_ASC])
				->all(),
			'id', 'userProfile.fullName'
		))->label('Teacher');
            ?>  
        </div>
        <div class="col-md-5">
		<?= $form->field($model, 'date')->widget(DatePicker::classname(), [
                'options' => [
                    'value' => Yii::$app->formatter->asDateTime($model->date),
					'readOnly' => true,
                ],
				'layout' => '{input}{remove}',
                'type' => DatePicker::TYPE_COMPONENT_APPEND,
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'dd-mm-yyyy',
                ],
            ])->label('Reschedule Date');
            ?>
        </div>
        </div>
        <div class="col-md-12">
			<div id="teacher-lesson"></div>
        </div>
	   <div class="clearfix"></div>
		<?php $locationId = Yii::$app->session->get('location_id'); ?>
   <div class="form-group">
        <?= Html::submitButton(Yii::t('backend', 'Save'), ['id' => 'lesson-edit-save', 'class' => 'btn btn-info', 'name' => 'button']) ?>
		<?= Html::a('Cancel', '#', ['class' => 'btn btn-default lesson-cancel']);
        ?>
		<div class="clearfix"></div>
	</div>
	<?php ActiveForm::end(); ?>
</div>