<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model backend\models\search\UserSearch */
/* @var $form yii\bootstrap\ActiveForm */
$roles = ArrayHelper::getColumn(
             Yii::$app->authManager->getRoles(),
    'description'
        )
?>
<div class="user-search m-t-10">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="row col-md-12">
		<div class="col-md-2">
            <?php echo $form->field($model, 'firstname') ?>
        </div>
        <div class="col-md-2">
            <?php echo $form->field($model, 'lastname') ?>
        </div>
        <div class="col-md-2">
            <?php echo $form->field($model, 'email') ?>
        </div>
        <div class="col-md-2 center form-group m-t-5">
            <br>
            <?php echo Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
