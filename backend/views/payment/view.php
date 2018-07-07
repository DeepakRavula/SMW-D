<?php

use yii\helpers\Url;
use yii\bootstrap\Html;
/* @var $this yii\web\View */
/* @var $model common\models\Blog */
/* @var $form yii\bootstrap\ActiveForm */

?>

<?= $this->render('payment-summary', ['model' => $model]); ?>

<?php $lessonCount = $lessonDataProvider->getCount(); ?>
<?php if ($lessonCount > 0) : ?>
    <?= Html::label('Lessons', ['class' => 'admin-login']) ?>

    <?= $this->render('/payment/_lesson-line-item', [
        'model' => $model,
        'canEdit' => $canEdit,
        'lessonDataProvider' => $lessonDataProvider,
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


<script>
	$(document).ready(function () {
		var header = '<?= $this->render('modal-action'); ?>';
        $('#popup-modal').find('.modal-header').html(header);
	    $('#popup-modal .modal-dialog').css({'width': '1000px'});
	});

    $(document).on('modal-success', function(event, params) {
        var url = "<?= Url::to(['payment/index']); ?>";
        $.pjax.reload({url: url, container: "#payment-listing", replace: false, timeout: 4000});
        return false;
    });

    $(document).on("click", "#print", function () {
        var url = '<?= Url::to(['print/payment','id' => $model->id]); ?>';
        window.open(url, '_blank');
    });

    $(document).off("click", "#edit").on("click", "#edit", function () {
        $('#modal-spinner').show();
        var url = '<?= Url::to(['payment/update', 'id' => $model->id]); ?>';
        $.ajax({
            url    : url,
            type   : 'get',
            dataType: "json",
            success: function(response)
            {
                if(response.status)
                {
                    $('#modal-content').html(response.data);
                    $('#popup-modal').modal('show');
                    $('#modal-spinner').hide();
                }
            }
        });
        return false;
    });
</script>
