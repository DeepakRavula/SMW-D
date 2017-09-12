<?php
use yii\bootstrap\Modal;
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
    </dl>
<?php endif;?>
<?php LteBox::end() ?>
<?php \yii\widgets\Pjax::end(); ?>
<?php
Modal::begin([
    'header' => '<h4 class="m-0">Payment Preference</h4>',
    'id' => 'payment-preference-modal',
]);

?>
<div id="payment-preference-content"></div>
<?php
Modal::end();

?>

<script>
    $(document).ready(function () {
        $(document).on('click', '#payment-preference', function (e) {
            modifyPaymentPreference();
        });

        $(document).on('click', '#cancel', function (e) {
            $('#payment-preference-modal').modal('hide');
            return false;
        });
    });

    $(document).on("click", "#payment-preference-grid tbody > tr", function () {
        modifyPaymentPreference();
    });

    $(document).on("beforeSubmit", "#payment-preference-form", function () {
        $.ajax({
            url: $(this).attr('action'),
            type: 'post',
            dataType: "json",
            data: $(this).serialize(),
            success: function (response)
            {
                if (response.status)
                {
                    $('#payment-preference-modal').modal('hide');
                    $.pjax.reload({container: '#payment-preference-listing', replace: false, timeout: 4000});
                }
            }
        });
        return false;
    });

    $(document).on("click", ".payment-preference-delete", function () {
        var preferenceId = $(this).attr('preferenceId');
        var status = confirm("Are you sure to delete this?");
        var params = $.param({'id': preferenceId});
        if (status) {
            $.ajax({
                url: '<?php echo Url::to(["customer-payment-preference/delete"]); ?>?' + params,
                type: 'post',
                dataType: "json",
                data: $(this).serialize(),
                success: function (response)
                {
                    if (response.status)
                    {
                        $('#payment-preference-modal').modal('hide');
                        $.pjax.reload({container: '#payment-preference-listing', replace: false, timeout: 4000});
                    }
                }
            });
        }
        return false;
    });

    function modifyPaymentPreference() {
        $.ajax({
            url: '<?= Url::to(['customer-payment-preference/modify', 'id' => $model->id]); ?>',
            type: 'get',
            dataType: "json",
            success: function (response)
            {
                if (response.status)
                {
                    $('#payment-preference-content').html(response.data);
                    $('#payment-preference-modal').modal('show');
                }
            }
        });
        return false;
    }
</script>	