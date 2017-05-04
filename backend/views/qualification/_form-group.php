<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\User;
use common\models\Program;
use yii\helpers\Url;
use kartik\select2\Select2;
use common\models\Qualification;

/* @var $this yii\web\View */
/* @var $model common\models\Qualification */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="lesson-qualify p-10">

<?php $form = ActiveForm::begin([
    'id' => 'group-qualification-form',
	'action' => Url::to(['qualification/add-group', 'id' => $userModel->id]),
]); ?>
	<?php 
		$groupQualifications = Qualification::find()
		->joinWith(['program' => function($query) {
			$query->group();
		}])
		->andWhere(['teacher_id' => $userModel->id])
		->all();
		$groupQualificationIds = ArrayHelper::getColumn($groupQualifications, 'program_id'); 
		$groupPrograms = Program::find()->group()
			->andWhere(['NOT IN', 'program.id', $groupQualifications])->all();
?>
   <div class="row">
	   <div class="col-md-6">
            <?= $form->field($model, 'program_id')->widget(Select2::classname(), [
	    		'data' => ArrayHelper::map($groupPrograms, 'id', 'name'),
				'options' => [
					'id' => 'program'
				],
				'pluginOptions' => [
					'allowClear' => true,
					'multiple' => false,
				],
			]); ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'rate')->textInput();?>
        </div>
    <div class="col-md-12 p-l-20 form-group">
        <?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-info', 'name' => 'button']) ?>
        
        <?= Html::a('Cancel', '', ['class' => 'btn btn-default qualification-cancel']);?>
        <div class="clearfix"></div>
	</div>
	</div>
	<?php ActiveForm::end(); ?>
</div>
