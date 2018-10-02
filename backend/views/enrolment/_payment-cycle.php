<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;
use common\components\gridView\KartikGridView;
use yii\widgets\Pjax;
use yii\bootstrap\ActiveForm;
use kartik\switchinput\SwitchInput;

?>

    <?php $columns = [
            'startDate:date',
            'endDate:date',
            [
                'label' => 'Status',
                'value' => function ($data) {
                    $status = null;
                    if (!$data->hasLessonPayment()) {
                        $status = 'Owing';
                    }
                    if ($data->hasPartialyPaidLesson()) {
                        $status = 'Partialy Paid';
                    }
                    if ($data->isFullyPaid()) {
                        $status = 'Paid';
                    } 
                    return $status;
                }
            ],
            [
                'label' => 'Invoiced',
                'value' => function ($data) {
                    $invoiceStatus = null;
                    if ($data->hasInvoicedLesson()) {
                        $invoiceStatus = 'Invoiced';
                    } else {
                        $invoiceStatus = 'Not Invocied';
                    }
                    return $invoiceStatus;
                }
            ],
        ] ?>
<?php Pjax::begin(['id' => 'payment-cycle-listing']); ?>
    <?= KartikGridView::widget([
        'dataProvider' => $paymentCycleDataProvider,
        'tableOptions' => ['class' => 'table table-bordered'],
        'summary' => false,
        'emptyText' => false,
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'options' => ['id' => 'enrolment-payment-cycle-grid'],
        'columns' => $columns,
    ]); ?>
<?php Pjax::end(); ?>

<script>
    $('.enrolment-preferred-payment').on('switchChange.bootstrapSwitch', function(event, state) {
        var paymentCycleId = ($(this).parents('tr') ).data('key');
        var params = $.param({'state' : state | 0, 'paymentCycleId' : paymentCycleId});
	    $.ajax({
            url    : '<?= Url::to(['enrolment/update-preferred-payment-status']) ?>?' + params,
            type   : 'POST',
            dataType: "json",
            data   : $(this).serialize()
        });
        return false;
    });
</script>
