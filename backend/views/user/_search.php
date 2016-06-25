<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model backend\models\search\UserSearch */
/* @var $form yii\bootstrap\ActiveForm */
$roles = ArrayHelper::getColumn(
         	Yii::$app->authManager->getRoles(),'description'
        )
?>
<div class="user-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="row">
    <div class="col-md-3">
        <?php echo $form->field($model, 'role_name')->dropDownList($roles, ['prompt'=>'Select']);?>
    </div>
    <div class="col-md-3">
        <?php echo $form->field($model, 'email') ?>
    </div>
    
    <div class="col-md-3 form-group m-t-5">
        <br>
        <?php echo Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
    </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
