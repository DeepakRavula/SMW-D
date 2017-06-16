<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Holiday */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="holiday-form">
<?php   $url = Url::to(['holiday/update', 'id' => $model->id]);
            if ($model->isNewRecord) {
               $url = Url::to(['holiday/create']);
            }
        $form = ActiveForm::begin([
        'id' => 'holiday-form',
        'action' => $url,
    ]); ?>

    <div class="row">
        <div class="col-xs-4">
            <?php echo $form->field($model, 'date')->widget(\yii\jui\DatePicker::classname(), [
                    'options' => ['class' => 'form-control'],
                    'clientOptions' => [
                        'changeMonth' => true,
                        'changeYear' => true,
                    ],
                ]); ?>
        </div>
		 <div class="col-xs-5">
            <?php echo $form->field($model, 'description')->textInput(); ?>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="row-fluid">
    <div class="form-group">
       <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
        <?php if (!$model->isNewRecord) {
        	echo Html::a('Cancel', '', ['class' => 'btn btn-default holiday-cancel']);
            }
        ?>
    </div>
    <div class="clearfix"></div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
