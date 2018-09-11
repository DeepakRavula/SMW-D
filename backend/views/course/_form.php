<?php

use common\models\Program;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use kartik\depdrop\DepDrop;
use common\models\LocationAvailability;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\time\TimePicker;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model common\models\GroupCourse */
/* @var $form yii\bootstrap\ActiveForm */

?>
<div id="error-notification" style="display: none;" class="alert-danger alert fade in"></div>
	<div class="row">
            <div class="col-md-12">
		<div class="col-md-10">
			<?php
            echo $form->field($model, 'programId')->widget(Select2::classname(), [
                'data' =>ArrayHelper::map(Program::find()->notDeleted()->group()->active()
                        ->all(), 'id', 'name'),
                            'options' => [
                                    'id' => 'course-programid'
                ],
                'pluginOptions' => [
                                    'multiple' => false,
                                    'placeholder' => 'Select Program',
                ],
            ]);
            ?>
		</div>
		<div class="col-md-10">
			<?php
            // Dependent Dropdown
            echo $form->field($model, 'teacherId')->widget(DepDrop::classname(), [
                'options' => ['id' => 'course-teacherid'],
                                 'type' => DepDrop::TYPE_SELECT2,
                'pluginOptions' => [
                    'depends' => ['course-programid'],
                    'placeholder' => 'Select...',
                    'url' => Url::to(['course/teachers']),
                ],
            ])->label('Teacher');
            ?>
		</div>
		<div class="col-md-10">
			<?=
            $form->field($model, 'duration')->widget(TimePicker::classname(), [
                'pluginOptions' => [
                    'showMeridian' => false,
                    'defaultTime' => (new \DateTime('00:30'))->format('H:i'),
                ],
                'options' => [
                    'class' => 'duration'
                ]
            ]);
            ?>
		</div>
		<div class="col-md-10">
			<?= $form->field($model, 'weeksCount')->textInput()->label('Number Of Weeks'); ?>
		</div>
            </div>
		<div class="clearfix"></div>
		<div class="padding-v-md">
        <div class="line line-dashed"></div>
    </div>
        </div>
     
      
