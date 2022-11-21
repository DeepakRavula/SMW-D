<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\UserProfile */
/* @var $form yii\bootstrap\ActiveForm */

$this->title = Yii::t('backend', 'Edit Profile')
?>

<div class="user-profile-form p-t-10 p-b-20">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row-fluid">
        <div class="col-md-3">
            <?php echo $form->field($model, 'firstname')->textInput(['maxlength' => 255]) ?>
        </div>
        <div class="col-md-3">
            <?php echo $form->field($model, 'lastname')->textInput(['maxlength' => 255]) ?>
        </div>
		<div class="col-md-3">
			<?php echo $form->field($model, 'email') ?>
		</div>
        <div class="col-md-6">
            <?php echo Html::submitButton(Yii::t('backend', 'Update'), ['class' => 'btn btn-info']) ?>
        </div>
        <div class="clearfix"></div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
