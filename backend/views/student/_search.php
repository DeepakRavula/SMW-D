<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;


/* @var $this yii\web\View */
/* @var $model backend\models\search\UserSearch */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="user-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    
    <?php echo $form->field($model, 'first_name') ?>

    <?php echo $form->field($model, 'last_name') ?>
    
    <?php
        //$customer = ArrayHelper::map(Student::customer()->all(), 'id', 'id'));
        $a= ['1' => 'Yes', '0' => 'No'];
        echo $form->field($model, 'customer_id')->dropDownList($a,['prompt'=>'Select Option']);
    ?>

    <div class="form-group">
        <?php echo Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
