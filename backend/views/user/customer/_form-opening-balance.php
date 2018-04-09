<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Payments */
/* @var $form yii\bootstrap\ActiveForm */
?>

<?php $form = ActiveForm::begin([
    'id' => 'modal-form',
    'action' => Url::to(['customer/add-opening-balance', 'id' => $userModel->id]),
]); ?>
<div class="row">
    <div class="col-md-8">
        <?php echo $form->field($model, 'amount')->textInput(['class' => 'right-align form-control']); ?>
    </div>
</div>

<?php ActiveForm::end(); ?>