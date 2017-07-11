<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\jui\DatePicker;
use kartik\depdrop\DepDrop;
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
        'action' => ['daily-schedule/'.$searchModel->slug],
        'method' => 'get',
    ]); ?>
    <div class="row">
    <div class="col-md-3">
         <?php
         $location = Location::find()->where(['like', 'slug', $searchModel->slug])->one();
         $locationId = $location->id;
         $teachers = ArrayHelper::map(
                    User::find()
                        ->notDeleted()
                        ->allteachers($locationId)
                        ->all(), 'id', 'publicIdentity');

        echo $form->field($searchModel, 'findTeacher')->widget(DepDrop::classname(), [
                'data' => $teachers,
                'type' => DepDrop::TYPE_SELECT2,
                'options' => [
                    'id' => 'lesson-teacher',
                    'placeholder' => 'Select teacher',
                ],
                'pluginOptions' => [
                    'depends' => ['lesson-program'],
                    'url' => Url::to(['/course/teachers'])
                ]
            ]);
        ?>

         <?php echo Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
    </div>  
   
    <div class="col-md-3 form-group m-t-20">
        <div class="clearfix"></div>
    </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
