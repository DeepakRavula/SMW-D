<?php

use yii\helpers\Url;
use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;
/* @var $this yii\web\View */
/* @var $model common\models\Blog */
/* @var $form yii\bootstrap\ActiveForm */

?>
<?= Html::a('<i title="Mail" class="fa fa-envelope-o"></i>', '#', [
    'id' => 'payment-mail-button',
    'class' => 'm-r-10 btn btn-box-tool']) ?>
<?php $form = ActiveForm::begin([
    'id' => 'modal-form',
    'action' => Url::to(['payment/view', 'PaymentEditForm[paymentId]' => $model->id])
]); ?>

<?php ActiveForm::end(); ?>

<?php $lessonCount = $lessonDataProvider->getCount(); ?>
<?php $groupLessonCount = $groupLessonDataProvider->getCount(); ?>
<?php $invoiceCount = $invoiceDataProvider->getCount(); ?>
<?php if ($lessonCount <= 0 && $invoiceCount<=0 && $model->amount>0) : ?>
<div class="text-center"><h2>You didn't select any lessons or invoices</h2><br/><h4>so we'll save this payment as credit to your customer account</h4> </div>
<?php else:?>
<?php if ($lessonCount > 0) : ?>
    <?= Html::label('Lessons', ['class' => 'admin-login']) ?>

    <?= $this->render('/payment/_lesson-line-item', [
        'model' => $model,
        'canEdit' => $canEdit,
        'lessonDataProvider' => $lessonDataProvider,
    ]);
    ?>
<?php endif; ?>


<?php if ($groupLessonCount > 0) : ?>
    <?= Html::label('Group Lessons', ['class' => 'admin-login']) ?>

    <?= $this->render('/payment/_group-lesson-line-item', [
        'model' => $model,
        'canEdit' => $canEdit,
        'lessonDataProvider' => $groupLessonDataProvider,
    ]);
    ?>
<?php endif; ?>

<?php $invoiceCount = $invoiceDataProvider->getCount(); ?>
<?php if ($invoiceCount > 0) : ?>
    <?= Html::label('Invoices', ['class' => 'admin-login']) ?>
    <?= $this->render('/payment/_invoice-line-item', [
        'model' => $model,
        'canEdit' => $canEdit,
        'invoiceDataProvider' => $invoiceDataProvider,
    ]);
    ?>
<?php endif; ?>
    <?php endif;?>

<script>
	$(document).ready(function () {
		var header = '<?= $this->render('payment-summary', ['model' => $model]); ?>';
        var url = '<?= Url::to(['payment/delete','id' => $model->id]); ?>';
        $('#popup-modal').find('.modal-header').html(header);
        $('.modal-delete').show();
        $('.modal-save-all').show();
        $('.modal-button').show();
        $('.modal-save-all').text('Print');
        $('.modal-button').text('Edit');
        $(".modal-delete").attr("action", url);
        $('#popup-modal .modal-dialog').css({'width': '1000px'});
	});

    $(document).on("click", ".modal-save-all", function () {
        var url = '<?= Url::to(['print/payment','id' => $model->id]); ?>';
        window.open(url, '_blank');
    });

   $(document).on('click', '#payment-mail-button', function (e) {
            $.ajax({
                url    : '<?= Url::to(['email/payment', 'id' => $model->id]); ?>',
                type   : 'get',
                dataType: 'json',
                success: function(response)
                {
                    if (response.status) {
                        $('#modal-content').html(response.data);
                        $('#popup-modal').modal('show');
                        $('#popup-modal .modal-dialog').css({'width': '1000px'});
                        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Email Preview</h4>');
                        $('.modal-save').text('Send');
                    }
                }
            });
            return false;
  	});

    $(document).on('modal-error', function (event, params) {
        if (params.message) {
            $('#modal-popup-error-notification').html(params.message).fadeIn().delay(5000).fadeOut();
        }
    });

    $(document).off("click", ".modal-button").on("click", ".modal-button", function () {
        $('#modal-spinner').show();
        var url = '<?= Url::to(['payment/update', 'id' => $model->id]); ?>';
        $.ajax({
            url    : url,
            type   : 'get',
            dataType: "json",
            success: function(response)
            {
                $('#modal-spinner').hide();
                if (response.status) {
                    $('#modal-content').html(response.data);
                    $('#popup-modal').modal('show');
                } else {
                    $('#modal-popup-error-notification').text(response.message).fadeIn().delay(5000).fadeOut();
                }
            }
        });
        return false;
    });
</script>
