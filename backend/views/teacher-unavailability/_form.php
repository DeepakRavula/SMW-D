<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use kartik\time\TimePicker;
use kartik\datetime\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model common\models\Holiday */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="user-create-form row">
<?php   $url = Url::to(['teacher-unavailability/update', 'id' => $model->id]);
 $validationUrl = Url::to(['teacher-unavailability/validate', 'id' => $model->id]);
            if ($model->isNewRecord) {
                $url = Url::to(['teacher-unavailability/create', 'id' => $teacher->id]);
                $validationUrl = Url::to(['teacher-unavailability/validate']);
            }
        $form = ActiveForm::begin([
        'id' => 'modal-form',
        'action' => $url,
        'enableAjaxValidation' => true,
        'enableClientValidation' => false,
        'validationUrl' => $validationUrl,
    ]); ?>
    <div class="row">
		<?php
            echo $form->field($model, 'fromDateTime')->widget(DateTimePicker::classname(), [
                'type' => DateTimePicker::TYPE_INPUT,
                'pluginOptions' => [
                    'autoclose'=>true,
                    'format' => 'M dd, yyyy hh:ii'
                ]
            ]);
            ?>
		<?php
            echo $form->field($model, 'toDateTime')->widget(DateTimePicker::classname(), [
                'type' => DateTimePicker::TYPE_INPUT,
                'pluginOptions' => [
                    'autoclose'=>true,
                    'format' => 'M dd, yyyy hh:ii'
                ]
            ]);
            ?>		
		<?php
            echo $form->field($model, 'reason')->textarea(['rows' => 4]);?>
        <div class="clearfix"></div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
