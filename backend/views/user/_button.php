<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\User;
?>
<?php
$form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	'fieldConfig' => [
        'options' => [
            'tag' => false,
        ],
    ],
	]);
?>
<?php yii\widgets\Pjax::begin() ?>
<div id="show-all">
	<?php if ($searchModel->role_name === User::ROLE_CUSTOMER):?>
		<?= $form->field($searchModel, 'showAllCustomers')->checkbox(['data-pjax' => true]); ?>
    <?php endif; ?>
	 <?php if ($searchModel->role_name === User::ROLE_TEACHER):?>
		<?= $form->field($searchModel, 'showAllTeachers')->checkbox(['data-pjax' => true]); ?>
    <?php endif; ?> 
</div>
<?php \yii\widgets\Pjax::end(); ?>
<?php ActiveForm::end(); ?>