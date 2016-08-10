<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\PaymentMethod;
use common\models\Invoice;
use common\models\Allocation;
use common\models\BalanceLog;

/* @var $this yii\web\View */
/* @var $model common\models\Payments */
/* @var $form yii\bootstrap\ActiveForm */
?>

<h3>Cheque payment</h3>
<div class="payments-form p-l-20">
    <?php $form = ActiveForm::begin(); ?>
 	<div class="row">
        <div class="col-xs-3">
   			<?php echo $form->field($model, 'amount')->textInput() ?>
        </div>
	</div>
    <div class="form-group p-l-20">
       <?php echo Html::submitButton(Yii::t('backend', 'Pay Now'), ['class' => 'btn btn-success', 'name' => 'signup-button']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
