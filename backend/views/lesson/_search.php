<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use backend\models\search\LessonSearch;
use yii\helpers\ArrayHelper;
use yii\jui\DatePicker;

/* @var $this yii\web\View */
/* @var $model backend\models\search\UserSearch */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="user-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="row">   
    <div class="col-md-3">
        <?php echo $form->field($model, 'lessonStatus')->dropDownList(LessonSearch::lessonStatuses())->label('Status');?>
    </div>
    
    <div class="col-md-3">
        <?php echo $form->field($model, 'fromDate')->widget(DatePicker::classname(), [
            'options'=>[
                'class' => 'form-control'
            ]   
        ]) ?>
    </div>
    <div class="col-md-3">
        <?php echo $form->field($model, 'toDate')->widget(DatePicker::classname(), [
            'options'=>[
                'class' => 'form-control'
            ]
        ]) ?>
    </div>
    <div class="col-md-3 form-group m-t-5">
        <br>
        <?php echo Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
    </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
