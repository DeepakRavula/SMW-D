<?php

use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
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
        <?= $form->field($model, 'amount')->textInput(['class' => 'right-align form-control']); ?>
    </div>
</div>
<?php $list = [0 => 'Owing', 1 => 'Credit']; ?>
<div class="row">
    <div class="col-md-8">
        <?= $form->field($model, 'isCredit')->radioList($list)->label(false); ?>
    </div>
</div>

<?php ActiveForm::end(); ?>