<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\bootstrap\Html;
/* @var $this yii\web\View */
/* @var $model common\models\Blog */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="payment-form">
<?php 
        $url = Url::to(['payment/updatepayment', 'id' => $model->id]);
        $form = ActiveForm::begin([
        'id' => 'modal-form',
        'action' => $url,
    ]); ?>

</div>
    <?php ActiveForm::end(); ?>
<?= Html::label('Lessons', ['class' => 'admin-login']) ?>
    <?= $this->render('/payment/_lesson-line-item', [
        'model' => $model,
        'lessonDataProvider' => $lessonDataProvider,
    ]);
    ?>

    <?= Html::label('Invoices', ['class' => 'admin-login']) ?>
    <?= $this->render('/payment/_invoice-line-item', [
        'model' => $model,
        'invoiceDataProvider' => $invoiceDataProvider,
    ]);
    ?>
<script>
	$(document).ready(function () {
		   $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Payment</h4>');
			$('#popup-modal .modal-dialog').css({'width': '1000px'});
	});
    $(document).on('modal-success', function(event, params) {
        var url = "<?php echo Url::to(['payment/index']); ?>";
        $.pjax.reload({url: url, container: "#payment-listing", replace: false, timeout: 4000});
        return false;
    });
</script>
