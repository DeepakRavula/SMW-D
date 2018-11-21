<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use kartik\select2\Select2;
use common\models\Classroom;
use yii\helpers\ArrayHelper;
use common\models\Location;

/* @var $this yii\web\View */
/* @var $model common\models\Payments */
/* @var $form yii\bootstrap\ActiveForm */
?>
<style>
    .col-xs-3 {
        width: 23%;
    }
</style>
<div id="edit-classroom" class="edit-classroom">
    <?php $form = ActiveForm::begin([
        'id' => 'modal-form',
        'action' => Url::to(['private-lesson/edit-classroom', 'EditClassroom[lessonIds]' => $model->lessonIds]),
    ]); ?>
       <div class="row">
        <div class="col-md-6">
            <?php
            $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
            $classrooms = ArrayHelper::map(
                Classroom::find()->notDeleted()->andWhere(['locationId' => $locationId])->orderBy(['name' => SORT_ASC])->all(),
                    'id', 'name');
            echo $form->field($model, 'classroomId')->widget(Select2::classname(), [
                'data' => $classrooms,
                'options' => [
                    'placeholder' => 'classroom',
                    'id' => 'change-classroom'
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ]
            ]);
            ?>
        </div>
        </div>
   
    <?php ActiveForm::end(); ?>
</div>

<script>
    $(document).ready(function() {
        $('#popup-modal .modal-dialog').css({'width': '600px'});
        $('#popup-modal .modal-body').css({'height': '200px'});
        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Edit Classroom</h4>');   
    });
</script>
