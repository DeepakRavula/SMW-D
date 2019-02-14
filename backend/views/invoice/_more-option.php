<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
?>

<?php Pjax::begin(['id' => 'invoice-more-option', 'timeout' => 6000]); ?>
    <div class="dropdown">
        <i class="fa fa-gear dropdown-toggle" data-toggle="dropdown"></i>
        <ul class="dropdown-menu dropdown-menu-right">
            <li><a id="receive-payments" href='#' data-url='<?= Url::to(['payment/receive', 'PaymentFormLessonSearch[userId]' => $model->user_id]); ?>'>Receive Payment</a></li>
            <?php if ($model->isInvoice()) : ?>
                <?php if (!$model->isVoid && !$model->isPaymentCreditInvoice()) : ?>
                    <li><a id="void" href="#">Void</a></li>
                <?php else : ?>
                    <li><a class="multiselect-disable" href="#">Void</a></li>
                <?php endif; ?>
            <?php endif; ?>
        </ul>
    </div>

<?php Pjax::end(); ?>

<script>
	$(document).off('click', '#receive-payments').on('click', '#receive-payments', function () {
        $.ajax({
            url    : $(this).attr('data-url'),
            type   : 'get',
            dataType: 'json',
            success: function(response)
            {
                if (response.status) {
                    $('#modal-content').html(response.data);
                    $('#popup-modal').modal('show');
                }
            }
        });
        return false;
    });
</script>