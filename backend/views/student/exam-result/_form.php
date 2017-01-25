<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\date\DatePicker;
use yii\helpers\ArrayHelper;
use common\models\User;
use wbraganca\selectivity\SelectivityWidget;

/* @var $this yii\web\View */
/* @var $model common\models\Lesson */
/* @var $form yii\bootstrap\ActiveForm */
?>
 <?php
 $locationId = Yii::$app->session->get('location_id');
 $teachers = ArrayHelper::map(User::find()
		->joinWith(['userLocation ul' => function ($query) {
			$query->joinWith('teacherAvailability');
		}])
		->join('INNER JOIN', 'rbac_auth_assignment raa', 'raa.user_id = user.id')
		->where(['raa.item_name' => 'teacher'])
		->andWhere(['ul.location_id' => Yii::$app->session->get('location_id')])
		->all(),
	'id', 'userProfile.fullName'
);?>
<div class="lesson-form">
<?php $form = ActiveForm::begin([
	'id' => 'exam-result-form',
]); ?>
<div class="row">
	<div class="col-md-6">
		<?=  $form->field($model, 'date')->widget(DatePicker::classname(), [
				'options' => [
					'value' => !empty($model->date) ? Yii::$app->formatter->asDate($model->date) : Yii::$app->formatter->asDate(new \DateTime()),
		   ],
			'type' => DatePicker::TYPE_COMPONENT_APPEND,
			'pluginOptions' => [
				'autoclose' => true,
				'format' => 'dd-mm-yyyy',
			],
		  ]);
		?>
    </div>
	<div class="col-md-6">
		<?=  $form->field($model, 'mark')->textInput();?>
    </div>
	<div class="col-md-6">
		<?=  $form->field($model, 'level')->textInput();?>
    </div>
	<div class="col-md-6">
		<?=  $form->field($model, 'program')->textInput();?>
    </div>
	<div class="col-md-6">
		<?=  $form->field($model, 'type')->textInput();?>
    </div>
	<div class="col-md-6">
        <?=
        $form->field($model, 'teacherId')->widget(SelectivityWidget::classname(), [
            'pluginOptions' => [
                'allowClear' => true,
                'multiple' => false,
                'items' => $teachers,
                'placeholder' => 'Select Teacher',
            ],
        ]);
    	?>
        </div>
        <div class="clearfix"></div>
    <div class="col-md-12 p-l-20 form-group">
		<?=  $form->field($model, 'id')->hiddenInput()->label(false);?>
        <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
    </div>
    </div>
<?php ActiveForm::end(); ?>

</div>
