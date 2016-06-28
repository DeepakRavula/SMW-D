<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;


/* @var $this yii\web\View */
/* @var $model backend\models\search\UserSearch */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="user-search m-t-10">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="row col-md-12">
    <div class="col-md-3">
    <?php echo $form->field($model, 'first_name') ?>
    </div>
    <div class="col-md-3">
    <?php echo $form->field($model, 'last_name') ?>
    </div>
    <div class="col-md-3">
    <?php
        //$customer = ArrayHelper::map(Student::customer()->all(), 'id', 'id'));
        $a= ['1' => 'Yes', '0' => 'No'];
        echo $form->field($model, 'customer_id')->dropDownList($a,['prompt'=>'Select Option']);
    ?>
    </div>
    <div class="col-md-3">
    <div class="form-group m-t-5">
        <br>
        <?php echo Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
    </div>
    </div>
    </div>
    <?php ActiveForm::end(); ?>
    
</div>
