<?php

use yii\helpers\ArrayHelper;
use common\models\Program;
use kartik\select2\Select2;
use kartik\depdrop\DepDrop;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

?>

<div class="user-create-form">
    <?php $privatePrograms = ArrayHelper::map(Program::find()
            ->active()
            ->andWhere(['type' => Program::TYPE_PRIVATE_PROGRAM])
            ->all(), 'id', 'name');
    $form = ActiveForm::begin(['id' => 'course-change']);?>
    <div class="row">
        <div class="form-group col-lg-6">
            <?php echo $form->field($model, 'programId')->widget(Select2::classname(), [
                'data' => $privatePrograms,
                'options' => [
                    'placeholder' => 'program',
                    'id' => 'change-course-program'
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ]
            ]); ?>
        </div>
        <div class="col-lg-6">
            <?php
                // Dependent Dropdown
                echo $form->field($model, 'teacherId')->widget(DepDrop::classname(),
                    [
                    'type' => DepDrop::TYPE_SELECT2,
                    'options' => [
                        'placeholder' => 'teacher',
                        'id' => 'change-course-teacher'
                    ],
                    'pluginOptions' => [
                        'depends' => ['change-course-program'],
                        'url' => Url::to(['course/teachers'])
                    ],
                ]);
            ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<script>
    $(document).ready(function () {
        $('#change-course-program').trigger('change');
    });
    $(document).off('click', '.change-program-teacher-save').on('click', '.change-program-teacher-save', function () {
        var lessonIds = $('#unschedule-lesson-index').yiiGridView('getSelectedRows');
        var params = $.param({ ids: lessonIds });
        $.ajax({
            url    : '<?= Url::to(['course/change']) ?>?' + params,
            type   : 'post',
            dataType: "json",
            data   : $('#course-change').serialize(),
            success: function(response)
            {
                if(response.status)
                {
                    $('#change-program-teacher-modal').modal('hide');
                    $.pjax.reload({container: '#lesson-index', timeout: 6000, async:false});
                    $('#enrolment-delete-success').html("Lessons are changed to different course").
                                fadeIn().delay(5000).fadeOut();
                }
            }
        });
        return false;
    });
    
    $(document).off('click', '.change-program-teacher-cancel').on('click', '.change-program-teacher-cancel', function () {
        $('#change-program-teacher-modal').modal('hide');
        return false;
    });
</script>