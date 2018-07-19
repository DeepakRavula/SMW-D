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
<?= Yii::$app->formatter->format($model->getTotal($model->id), ['currency', 'USD', [
        \NumberFormatter::MIN_FRACTION_DIGITS => 2,
        \NumberFormatter::MAX_FRACTION_DIGITS => 2,
    ]]); ?> &nbsp;&nbsp;
    <?=	Html::a(
            '<i title="Delete" class="fa fa-trash"></i>',
            ['proforma-invoice/delete', 'id' => $model->id],
        [
            'class' => 'm-r-10 btn btn-box-tool ',
            'data' => [
                'confirm' => 'Are you sure you want to delete this invoice?',
            ],
            'id' => 'delete-button',
        ]
        )
        ?>
    <div class="dropdown">
        <i class="fa fa-gear dropdown-toggle" data-toggle="dropdown"></i>
        <ul class="dropdown-menu dropdown-menu-right">
            <li><a id="proforma-receive-payment" href="#">Receive Payment</a></li>
            <li><a id="proforma-invoice-mail-button" href="#">Mail</a></li>
            <li><a id="proforma-print-btn" href="#">Print</a></li>
        </ul>
    </div>
   
</div>

<?php Pjax::end();?>

<script>
	$(document).off('click', '#proforma-receive-payment').on('click', '#proforma-receive-payment', function () {
        $.ajax({
            url    : '<?= Url::to(['payment/receive', 'PaymentFormLessonSearch[userId]' => $model->userId, 
                'PaymentFormGroupLessonSearch[userId]' => $model->userId]); ?>',
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
    });
</script>
