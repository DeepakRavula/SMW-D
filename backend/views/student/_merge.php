<?php
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

?>
<div id="error-notification" style="display:none;" class="alert-danger alert fade in"></div>


<div>
    
    <?php $form = ActiveForm::begin([
            'id' => 'modal-form',
            'action' => Url::to(['student/merge', 'id' => $model->id]),
    ]); ?>
    <div class="row">
        <div class="col-md-8">
            <?= $form->field($model, 'studentId')->widget(Select2::classname(), [
                'data' => ArrayHelper::map($students, 'id', 'fullName'),
                'options' => [
                                    'id' => 'student'
                ],
                'pluginOptions' => [
                                    'multiple' => false,
                                    'placeholder' => 'select student',
                ],
            ]); ?>
        </div>
        </div>
    </div>
	<?php ActiveForm::end(); ?>
</div>
<script>
    $(document).ready(function () {
        $('.modal-save').show();
        $('.modal-save').text('Merge');
        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Student Merge</h4>');
        $('#popup-modal .modal-dialog').css({'width': '400px'});
    });
</script>