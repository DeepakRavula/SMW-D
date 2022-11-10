<?php

use yii\helpers\ArrayHelper;
use common\models\Program;
use kartik\select2\Select2;
use kartik\depdrop\DepDrop;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use common\models\User;
use common\models\Location;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

?>

<div class="user-create-form">
    <?php $privatePrograms = ArrayHelper::map(Program::find()
            ->notDeleted()
            ->active()
            ->privateProgram()
            ->all(), 'id', 'name');
    
    $defaultTeacher = ArrayHelper::map(User::find()
            ->notDeleted()
            ->allTeachers()
            ->location(Location::findOne(['slug' => \Yii::$app->location])->id)
            ->all(), 'id', 'publicIdentity');
    
    $form = ActiveForm::begin([
        'id' => 'modal-form',
        'action' => Url::to(['course/change', 'LessonSearch[ids]' => $lessonIds]),
    ]);?>
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
                echo $form->field($model, 'teacherId')->widget(
                    DepDrop::classname(),
                    [
                    'type' => DepDrop::TYPE_SELECT2,
                    'data'=> $defaultTeacher,
                    'options' => [
                        'placeholder' => 'teacher',
                        'id' => 'change-course-teacher'
                    ],
                    'pluginOptions' => [
                        'depends' => ['change-course-program'],
                        'url' => Url::to(['course/teachers'])
                    ],
                ]
                );
            ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<script>
    $(document).on('modal-success', function(event, params) {
        $.pjax.reload({container: '#lesson-index', timeout: 6000, async:false});
        $('#enrolment-delete-success').html(params.message).
                    fadeIn().delay(5000).fadeOut();
        return false;
    });
    
    $(document).on('modal-close', function() {
        $.pjax.reload({container: '#lesson-index', timeout: 6000, async:false});
        return false;
    });
</script>