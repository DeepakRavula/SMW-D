<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Program;
use yii\helpers\Url;
use kartik\select2\Select2;
use common\models\Qualification;

/* @var $this yii\web\View */
/* @var $model common\models\Qualification */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="lesson-qualify">

<?php
if ($model->isNewRecord) {
    $url = Url::to(['qualification/create', 'id' => $teacherId, 'type' => $model->type]);
} else {
    $url = Url::to(['qualification/update', 'id' => $model->id]);
}
$form = ActiveForm::begin([
    'id' => 'modal-form',
    'action' => $url
]); ?>
    <?php $qualifications = Qualification::find()
        ->notDeleted()
        ->andWhere(['teacher_id' => $model->teacher_id])
        ->all();
        $teacherQualificationIds = ArrayHelper::getColumn($qualifications, 'program_id');
        $query = Program::find()->notDeleted()->active();
        if ($model->isNewRecord) {
            $query->andWhere(['NOT IN', 'program.id', $teacherQualificationIds]);
        }
    if ((int) $model->type === (int) Qualification::TYPE_HOURLY) {
        $query->privateProgram();
    } else {
        $query->group();
    }
    $programs = $query->all();
?>
   <div class="row">
	   
        <div class="col-md-8">
            <?php if ($model->isNewRecord) : ?>
                <?= $form->field($model, 'programs')->widget(Select2::classname(), [
                    'data' => ArrayHelper::map($programs, 'id', 'name'),
                    'options' => [
                        'multiple' => true
                    ]
                ]); ?>
            <?php else: ?>
                <?= $form->field($model, 'program_id')->widget(Select2::classname(), [
                    'data' => ArrayHelper::map($programs, 'id', 'name'),
                    'disabled' => !$model->isNewRecord
                ]); ?>
            <?php endif; ?>
        </div>
	   <?php if (Yii::$app->user->can('teacherQualificationRate')) : ?>
        <div class="col-md-4">
            <?= $form->field($model, 'rate')->textInput(['class' => 'right-align form-control']);?>
        </div>
	   <?php endif; ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<script>
    $(document).ready(function() {
        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Qualification</h4>');
        $('#popup-modal .modal-dialog').css({'width': '400px'});
        var newRecord = <?= $model->isNewRecord | 0; ?>;
        if (!newRecord) {
            $('.modal-save').hide();
            $('.modal-save-all').addClass('edit-qualification-save');
            $('.modal-save-all').show();
            $('.modal-save-all').text('save');
        }
    });
    $(document).off('click', '.edit-qualification-save').on('click', '.edit-qualification-save', function () {
        bootbox.confirm({
            message: "Modifying teacher's rate would affect all the future lesson teacher's cost. Do you want to continue?",
                callback: function(result){
                    if(result) {
                        $('.bootbox').modal('hide');
                        $.ajax({
                            url: $('#modal-form').attr('action'),
                            type: 'post',
                            dataType: "json",
                            data: $('#modal-form').serialize(),
                            success: function (response)
                            {
                                if (response.status) {
                                    $('#popup-modal').modal('hide');
                                    $.pjax.reload({container: '#qualification-grid', timeout: 6000, async:false});
                                }
                            }
                       });
                    } else {
                        $('.bootbox').modal('hide');
                        return false;
                    }
                }
        });
    });
</script>