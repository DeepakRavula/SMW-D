<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\date\DatePicker;
/* @var $this yii\web\View */
/* @var $model common\models\ClassroomUnavailability */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="classroom-unavailability-form">

    <?php
    $form = ActiveForm::begin([
            'id' => 'classroom-unavailability-form',
    ]);

    ?>
    <div class="row">
        <div class="col-lg-6">
            <?php
            echo $form->field($model, 'fromDate')->widget(DatePicker::classname(), [
                'options' => [
                    'value' => !empty($model->fromDate) ? Yii::$app->formatter->asDate($model->fromDate) : Yii::$app->formatter->asDate(new \DateTime()),
                ],
                'type' => DatePicker::TYPE_COMPONENT_APPEND,
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'dd-mm-yyyy',
                ],
            ]);

            ?>
        </div>
        <div class="col-lg-6">
            <?php
            echo $form->field($model, 'toDate')->widget(DatePicker::classname(), [
                'options' => [
                    'value' => !empty($model->toDate) ? Yii::$app->formatter->asDate($model->toDate) : Yii::$app->formatter->asDate(new \DateTime()),
                ],
                'type' => DatePicker::TYPE_COMPONENT_APPEND,
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'dd-mm-yyyy',
                ],
            ]);

            ?>
        </div>
        <div class="col-lg-10">
        <?php echo $form->field($model, 'reason')->textarea(['rows' => 6]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
    <div class="pull-right">
        <?php echo Html::a('Cancel', '#', ['class' => 'btn btn-default classroom-unavailability-cancel-button']); ?>
        <?php echo Html::submitButton('Save', ['class' => 'btn btn-info']) ?>
    </div>
    <div class="pull-left">        
        <?php if (!$model->isNewRecord) {
            echo Html::a('Delete', ['delete', 'id' => $model->id], [
                'id' => 'classroom-unavailability-delete-button',
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ]
            ]);
        }
        ?>
    </div>
        </div></div>
<?php ActiveForm::end(); ?>
</div>
