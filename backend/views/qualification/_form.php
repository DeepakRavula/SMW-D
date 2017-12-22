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
    'id' => 'qualification-form',
	'action' => Url::to(['qualification/update', 'id' => $model->id]),
	'enableAjaxValidation' => true,
	'enableClientValidation' => false
]); ?>
	<?php 
	$privateQualifications = Qualification::find()
		->joinWith(['program' => function($query) {
			$query->privateProgram();
		}])
		->andWhere(['teacher_id' => $model->id])
		->andWhere(['NOT', ['qualification.id' => $model->id]])
		->all();
		$privateQualificationIds = ArrayHelper::getColumn($privateQualifications, 'program_id'); 
		$privatePrograms = Program::find()->privateProgram()->active()
			->andWhere(['NOT IN', 'program.id', $privateQualificationIds])->all();
		$groupQualifications = Qualification::find()
		->joinWith(['program' => function($query) {
			$query->group();
		}])
		->andWhere(['teacher_id' => $model->id])
		->andWhere(['NOT', ['qualification.id' => $model->id]])
		->all();
		$groupQualificationIds = ArrayHelper::getColumn($groupQualifications, 'program_id'); 
		$groupPrograms = Program::find()->group()->active()
			->andWhere(['NOT IN', 'program.id', $groupQualifications])->all();
?>
   <div class="row">
	   <?php if($model->program->isPrivate()) : ?>
        <div class="col-md-6">
            <?= $form->field($model, 'program_id')->widget(Select2::classname(), [
	    		'data' => ArrayHelper::map($privatePrograms, 'id', 'name'),
				'disabled' => true,
				'pluginOptions' => [
					'multiple' => false,
				],
			]); ?>
        </div>
	   <?php else : ?>
	   <div class="col-md-6">
            <?= $form->field($model, 'program_id')->widget(Select2::classname(), [
	    		'data' => ArrayHelper::map($groupPrograms, 'id', 'name'),
				'disabled' => true,
				'pluginOptions' => [
					'multiple' => false,
				],
			]); ?>
        </div>
	   <?php endif; ?>
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
	   		<?php if(Yii::$app->user->can('administrator') || Yii::$app->user->can('owner')) : ?>
           <?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-info', 'name' => 'button']) ?>
			<?php endif; ?>
        <div class="clearfix"></div>
	</div>
        <div class="pull-left">
<?= Html::a('Delete', [
            'delete', 'id' => $model->id
        ],
        [
			'id' => 'qualification-delete',
            'class' => 'btn btn-danger pull-left',
            'data' => [
                'confirm' => 'Are you sure you want to delete this qualification?',
                'method' => 'post',
            ]
        ]); ?>
        </div>   
    </div>
	<?php ActiveForm::end(); ?>
</div>
</div>