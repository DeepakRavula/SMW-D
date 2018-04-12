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
<div class="show-all-top">
<div class="checkbox">
<div id="show-all" class="checkbox-btn">
		<?= $form->field($searchModel, 'showAll')->checkbox(['data-pjax' => true]); ?>
</div>
</div>
</div>
<?php \yii\widgets\Pjax::end(); ?>
<?php ActiveForm::end(); ?>
<div style="margin-top:-35px;margin-left:-30px">
    <?php if ($searchModel->role_name === User::ROLE_CUSTOMER):?>
    <?= Html::a('<i title="Print" class="fa fa-print"></i>', ['print/user?UserSearch%5Brole_name%5D=customer'], ['class' => 'btn btn-box-tool', 'target' => '_blank']) ?>
 <?php endif; ?>
    <?php if ($searchModel->role_name === User::ROLE_TEACHER):?>
    <?= Html::a('<i title="Print" class="fa fa-print"></i>', ['print/user?UserSearch%5Brole_name%5D=teacher'], ['class' => 'btn btn-box-tool', 'target' => '_blank']) ?>
 <?php endif; ?>
</div>