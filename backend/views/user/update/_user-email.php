<?php

use yii\helpers\Html;
use common\models\Label;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;

/* @var $model backend\models\UserForm */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $roles yii\rbac\Role[] */
/* @var $permissions yii\rbac\Permission[] */
?>
<div class="row user-create-form">
<?php
$form = ActiveForm::begin([
		'id' => 'edit-email-form',
	]);
?>
<div class="row">
		
</div>
	<div class="row pull-right">
		<?php echo Html::a('Cancel', '#', ['class' => 'btn btn-default edit-email-cancel-btn']); ?>        
		<?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-info', 'name' => 'signup-button']) ?>
	</div>
<?php ActiveForm::end(); ?>
</div>
