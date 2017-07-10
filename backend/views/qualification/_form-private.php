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
    'id' => 'qualification-form-create',
	'action' => Url::to(['qualification/create', 'id' => $userModel->id]),
]); ?>
	<?php 
	$privateQualifications = Qualification::find()
		->joinWith(['program' => function($query) {
			$query->privateProgram();
		}])
		->andWhere(['teacher_id' => $userModel->id])
		->all();
		$privateQualificationIds = ArrayHelper::getColumn($privateQualifications, 'program_id'); 
		$privatePrograms = Program::find()->privateProgram()->active()
			->andWhere(['NOT IN', 'program.id', $privateQualificationIds])->all();
?>
   <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'program_id')->widget(Select2::classname(), [
	    		'data' => ArrayHelper::map($privatePrograms, 'id', 'name'),
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
