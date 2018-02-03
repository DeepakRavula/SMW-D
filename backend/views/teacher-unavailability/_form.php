<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use kartik\datetime\DateTimePicker;
use kartik\time\TimePicker;
use yii\jui\DatePicker;
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
        'id' => 'unavailability-form',
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
                            'format' => 'M d,Y',
                        ],
                        'opens' => 'right',
                    ],
                ]);
                ?>
		<?php
            echo $form->field($model, 'fromTime')->widget(TimePicker::classname(), [
                'pluginOptions' => [
                    'defaultTime' => false,
                ],
            ]);
            ?>
		<?php
            echo $form->field($model, 'toTime')->widget(TimePicker::classname(), [
                'pluginOptions' => [
                    'defaultTime' => false,
                ],
            ]);
            ?>		
		<?php
            echo $form->field($model, 'reason')->textarea(['rows' => 4]);?>
        <div class="clearfix"></div>
    </div>
    <div class="row">
        <div class="pull-right">
            <?= Html::a('Cancel', '', ['class' => 'btn btn-default unavailability-cancel']);?>
       <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-info', 'name' => 'signup-button']) ?>
            </div>
        <div class="pull-left">
            <?php if (!$model->isNewRecord) : ?>
			<?= Html::a(
                'Delete',
                [
            'delete', 'id' => $model->id
        ],
        [
            'id' => 'unavailability-delete',
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this unavailability?',
                'method' => 'post',
            ]
        ]
            ); ?>
		<?php endif;?>
        </div>
    <div class="clearfix"></div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
