<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\jui\DatePicker;

/* @var $this yii\web\View */
/* @var $model common\models\Payments */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div>
    <?php $form = ActiveForm::begin([
        'id' => 'modal-form',
        'action' => Url::to(['lesson/edit-due-date', 'id' => $model->id]),
    ]); ?>
    <div class="row">
        <div class="col-xs-6 pull-left">
        <?= $form->field($model, 'dueDate')->widget(
                DatePicker::classname(), [
                    'value'  => Yii::$app->formatter->asDate($model->dueDate),
                    'dateFormat' => 'php:M d, Y',
                    'options' => [
                        'class' => 'form-control'
                    ],
                    'clientOptions' => [
                        'changeMonth' => true,
                        'yearRange' => '1500:3000',
                        'changeYear' => true
                    ]
                ]);
            ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<script>
    $(document).ready(function() {
        $('#popup-modal .modal-dialog').css({'width': '400px'});
        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Edit Due Date</h4>');
    });
</script>