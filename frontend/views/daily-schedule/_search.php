<?php

use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
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
    <div class="col-md-3 location-filter pull-right">
         <?php
         $locations = ArrayHelper::map(
            Location::find()
            ->all(), 'id', 'name');

        echo $form->field($searchModel, 'locationId')->widget(Select2::classname(), [
                'data' => $locations,
				 'theme' => Select2::THEME_BOOTSTRAP,
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