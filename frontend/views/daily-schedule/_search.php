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
		'id' => 'schedule-search',
        'action' => Url::to(['daily-schedule/index']),
        'method' => 'get',
    ]); ?>
    <div class="row">
    <div class="col-md-3">
         <?php
         $locations = ArrayHelper::map(
            Location::find()
            ->all(), 'id', 'name');

        echo $form->field($searchModel, 'locationId')->widget(Select2::classname(), [
                'data' => $locations,
                'options' => [
                    'id' => 'lesson-schedule',
                    'placeholder' => 'Location ',
                ],
            ])->label(false);
        ?>
    </div>  
    </div>
    <?php ActiveForm::end(); ?>

</div>
