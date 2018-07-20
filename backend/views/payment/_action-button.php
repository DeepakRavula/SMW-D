<?php
use yii\helpers\Html;
use yii\helpers\Url;

?>

<div class="dropdown">
    <i class="fa fa-gear dropdown-toggle" data-toggle="dropdown"></i>
    <ul class="dropdown-menu dropdown-menu-right">
    <li><a id="receive-payments" href="#">Receive Payment</a></li>
    <ul>
</div>

<script>
	$(document).off('click', '#receive-payments').on('click', '#receive-payments', function () {
        $.ajax({
            url    : '<?= Url::to(['payment/receive']); ?>',
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