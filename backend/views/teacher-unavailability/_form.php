<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use kartik\time\TimePicker;
use kartik\daterange\DateRangePicker;

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
            <label>Date Range</label>
                <?php
                echo DateRangePicker::widget([
                    'model' => $model,
                    'attribute' => 'dateRange',
                    'convertFormat' => true,
                    'initRangeExpr' => true,
                    'pluginOptions' => [
                        'autoApply' => true,
                        'locale' => [
                            'format' => 'M d, Y',
                        ],
                        'opens' => 'right',
                    ],
                ]);
                ?>
		<?php
            echo $form->field($model, 'fromTime')->widget(TimePicker::classname(), [
                'pluginOptions' => [
                    'defaultTime' => '12:00 PM',
                ],
            ]);
            ?>
		<?php
            echo $form->field($model, 'toTime')->widget(TimePicker::classname(), [
                'pluginOptions' => [
                    'defaultTime' => '01:00 PM',
                ],
            ]);
            ?>		
		<?php
            echo $form->field($model, 'reason')->textarea(['rows' => 4]);?>
        <div class="clearfix"></div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
