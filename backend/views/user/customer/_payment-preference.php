<?php
use yii\helpers\Url;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;

?>
    <?php
    if (empty($model->customerPaymentPreference)) {
        $boxTools = ['<i class="fa fa-plus m-r-10" id="payment-preference"></i>'];
    } else {
        $boxTools = ['<i class="fa fa-pencil m-r-10" id="payment-preference"></i>'];
    }

    ?>
    <?php
    yii\widgets\Pjax::begin([
        'id' => 'payment-preference-listing',
        'timeout' => 6000,
    ])

    ?>
    <?php
    LteBox::begin([
        'type' => LteConst::TYPE_DEFAULT,
        'boxTools' => $boxTools,
        'title' => 'Payment Preference',
        'withBorder' => true,
    ])

    ?>
<?php if (!(empty($model->customerPaymentPreference))): ?>
      <dl class="dl-horizontal">
        <dt>Day Of Month</dt>
        <dd><?= $model->customerPaymentPreference->dayOfMonth ?></dd>
        <dt>Payment Method</dt>
        <dd><?= $model->customerPaymentPreference->getPaymentMethodName() ?></dd>
        <dt>Expiry Date</dt>
        <dd><?= Yii::$app->formatter->asDate($model->customerPaymentPreference->expiryDate); ?></dd>
    </dl>
<?php endif;?>
<?php LteBox::end() ?>
<?php \yii\widgets\Pjax::end(); ?>

<script>
    $(document).off('click', '#payment-preference').on('click', '#payment-preference', function () {
        customer.modifyPaymentPreference();
    });

    $(document).on("click", "#payment-preference-grid tbody > tr", function () {
        customer.modifyPaymentPreference();
    });

    var customer = {
        modifyPaymentPreference :function() {
            $.ajax({
                url: '<?= Url::to(['customer-payment-preference/modify', 'id' => $model->id]); ?>',
                type: 'get',
                dataType: "json",
                success: function (response)
                {
                    if (response.status)
                    {
                        $('#modal-content').html(response.data);
                        $('#popup-modal').modal('show');
                        if (response.id) {
                            var param = $.param({ id: response.id });
                            var url = '<?= Url::to(['customer-payment-preference/delete']) ?>?' + param;
                            $('.modal-delete').show();
                            $(".modal-delete").attr("action", url);
                        }
                        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Payment Preference</h4>');
                        $('#popup-modal .modal-dialog').css({'width': '400px'});
                    }
                }
            });
            return false;
        }
    };
</script>	