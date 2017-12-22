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
		$groupPrograms = Program::find()->group()->active()->orderBy(['name' => SORT_ASC])
			->andWhere(['NOT IN', 'program.id', $groupQualificationIds])->all();
?>
   <div class="row">
	   <div class="col-md-6">
            <?= $form->field($model, 'program_id')->widget(Select2::classname(), [
	    		'data' => ArrayHelper::map($groupPrograms, 'id', 'name'),
				'options' => [
					'id' => 'program'
				],
				'pluginOptions' => [
					'multiple' => false,
				],
			]); ?>
        </div>
	   <?php if(Yii::$app->user->can('viewQualificationRate')) : ?>
        <div class="col-md-6">
            <?= $form->field($model, 'rate')->textInput(['class' => 'right-align form-control']);?>
        </div>
	   <?php endif; ?>
   </div>       
       <div class="row">
           <div class="col-md-12">
                 <div class="pull-right">
                     <?= Html::a('Cancel', '', ['class' => 'btn btn-default qualification-cancel']);?>
        <?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-info', 'name' => 'button']) ?>
        
        <div class="clearfix"></div>
	</div>
	</div>
	<?php ActiveForm::end(); ?>
</div>
</div>