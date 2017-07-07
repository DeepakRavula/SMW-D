<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\jui\DatePicker;

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
        'action' => ['report/tlist'],
        'method' => 'get',
    ]); ?>
    <div class="row">
    <div class="col-md-3">
        <?php echo $form->field($searchModel, 'findTeacher')->textInput()->label('Teacher Name'); ?>
    </div>  
   
    <div class="col-md-3 form-group m-t-20">
        <?php echo Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <div class="clearfix"></div>
    </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
