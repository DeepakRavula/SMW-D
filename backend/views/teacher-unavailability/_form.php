<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use kartik\datetime\DateTimePicker;
use kartik\time\TimePicker;
use yii\jui\DatePicker;

/* @var $this yii\web\View */
/* @var $model common\models\Holiday */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="holiday-form">
<?php   $url = Url::to(['teacher-unavailability/update', 'id' => $model->id]);
            if ($model->isNewRecord) {
               $url = Url::to(['teacher-unavailability/create', 'id' => $teacher->id]);
            }
        $form = ActiveForm::begin([
        'id' => 'unavailability-form',
        'action' => $url,
    ]); ?>

    <div class="row">
        <div class="col-xs-5">
			<?php echo $form->field($model, 'fromDate')->widget(DatePicker::classname(), [
			'options' => ['class' => 'form-control',
			],
		]); ?>
        </div>
		 <div class="col-xs-5">
			<?php
			 echo $form->field($model, 'toDate')->widget(DatePicker::classname(), [
            'options' => ['class' => 'form-control'],
            ]); ?>
        </div>
		 <div class="col-xs-5">
		<?php
            echo $form->field($model, 'fromTime')->widget(TimePicker::classname(), [
				'pluginOptions' => [
					'defaultTime' => false,
				],
			]);
            ?>
        </div>
		 <div class="col-xs-5">
		<?php
            echo $form->field($model, 'toTime')->widget(TimePicker::classname(), [
				'pluginOptions' => [
					'defaultTime' => false,
				],
			]);
            ?>
        </div>
		<div class="col-xs-9">
		<?php
            echo $form->field($model, 'reason')->textarea(['rows' => 4]);?>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="row-fluid">
    <div class="form-group">
       <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
		<?php if (!$model->isNewRecord) : ?>
        	<?= Html::a('Cancel', '', ['class' => 'btn btn-default unavailability-cancel']);?>
			<?= Html::a('Delete', [
            'delete', 'id' => $model->id
        ],
        [
			'id' => 'unavailability-delete',
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this unavailability?',
                'method' => 'post',
            ]
        ]); ?>
		<?php endif;?>
    </div>
    <div class="clearfix"></div>
    </div>
    <?php ActiveForm::end(); ?>

</div>