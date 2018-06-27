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
    	<?= $form->field($model, 'amount')->textInput()?>
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
    $(document).on('modal-success', function(event, params) {
        var url = "<?php echo Url::to(['payment/index']); ?>";
        $.pjax.reload({url: url, container: "#payment-listing", replace: false, timeout: 4000});
        return false;
    });
</script>
