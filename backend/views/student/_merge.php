<?php
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

?>
<div id="error-notification" style="display:none;" class="alert-danger alert fade in"></div>


<div>
    
    <?php $form = ActiveForm::begin([
            'id' => 'student-merge-form',
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
