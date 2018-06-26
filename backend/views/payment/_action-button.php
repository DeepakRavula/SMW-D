<?php
use yii\helpers\Html;
use yii\helpers\Url;

?>

<div class="btn-group-sm">
    <button class="btn dropdown-toggle" data-toggle="dropdown">More Action&nbsp;&nbsp;<span class="caret"></span></button>
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