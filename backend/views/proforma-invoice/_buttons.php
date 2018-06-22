<?php

use yii\helpers\Html;
use common\models\User;
use backend\models\search\InvoiceSearch;
use yii\widgets\Pjax;
use yii\helpers\Url;

?>
<?php $loggedUser = User::findOne(Yii::$app->user->id); ?>
<?php Pjax::Begin(['id' => 'invoice-header-summary']) ?>
<div id="invoice-header">
    <div class="btn-group-sm">
    <button class="btn dropdown-toggle" data-toggle="dropdown">More Action&nbsp;&nbsp;<span class="caret"></span></button>
    <ul class="dropdown-menu dropdown-menu-right">
	<li><a id="proforma-receive-payment" href="#">Receive Payment</a></li>
	<li><a id="proforma-invoice-mail-button" href="#">Mail</a></li>
    </ul>
<?= Yii::$app->formatter->format($model->getTotal($model->id), ['currency', 'USD', [
    \NumberFormatter::MIN_FRACTION_DIGITS => 2,
    \NumberFormatter::MAX_FRACTION_DIGITS => 2,
]]); ?> &nbsp;&nbsp;
</div>
</div>
<?php Pjax::end();?>
<script>
	$(document).off('click', '#proforma-receive-payment').on('click', '#proforma-receive-payment', function () {
        $.ajax({
            url    : '<?= Url::to(['payment/receive', 'PaymentForm[customerId]' => $model->userId]); ?>',
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