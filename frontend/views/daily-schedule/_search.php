<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\jui\DatePicker;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use common\models\User;
use common\models\Location;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model backend\models\search\TeacherScheduleSearch */
/* @var $form yii\bootstrap\ActiveForm */
?>
<style>
  .e1Div{
    right: 0 !important;
    top: -59px;
  }
</style>
<div class="user-search">
    <?php $form = ActiveForm::begin([
        'action' => Url::to(['daily-schedule/index']),
        'method' => 'get',
    ]); ?>
    <div class="row">
    <div class="col-md-3">
         <?php
         $teachers = ArrayHelper::map(
                    User::find()
                        ->notDeleted()
                        ->allteachers()
                        ->all(), 'id', 'publicIdentity');

        echo $form->field($searchModel, 'findTeacher')->widget(Select2::classname(), [
                'data' => $teachers,
                'options' => [
                    'id' => 'schedule-teacher',
                    'placeholder' => 'Location ',
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
        ?>
    </div>  
    </div>
    <?php ActiveForm::end(); ?>

</div>
