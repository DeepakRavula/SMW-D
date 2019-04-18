<?php

use yii\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\helpers\Html;
use common\components\gridView\KartikGridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Recurring Payments';
$this->params['breadcrumbs'][] = $this->title;
$this->params['action-button'] = Html::a(Yii::t('backend', '<i class="fa fa-plus f-s-18 m-l-10" aria-hidden="true"></i>'), '#', ['id' => 'recurring-payment']);
?> 
<div class="recurring-payment-index">  
<?php Pjax::begin(['id' => 'recurring-payment-listing']); ?>
<?= KartikGridView::widget([
    'dataProvider' => $dataProvider,
    'summary' => false,
    'emptyText' => false,
    'tableOptions' => ['class' => 'table table-bordered'],
    'headerRowOptions' => ['class' => 'bg-light-gray'],
    'columns' => [
            [
                'label' => 'Customer',
                'value' => function ($data) {
                    return $data->customer->publicIdentity;
                },
            ],
            [
                'label' => 'To Be Entered On',
                'value' => function ($data) {
                    return Yii::$app->formatter->asDate($data->startDate);
                },
            ],
            [
                'label' => 'Frequency',
                'value' => function ($data) {
                    return $data->paymentFrequency->name;
                },
            ],
            [
                'label' => 'Expiry Date',
                'value' => function ($data) {
                    return $data->expiryDate ? (new \DateTime($data->expiryDate))->format('M d, Y') : null;
                },
            ],
            [
                'label' => 'Method',
                'value' => function ($data) {
                    return $data->paymentMethod->name;
                },
            ],
            [
                'label' => 'Amount',
                'value' => function ($data) {
                    return Yii::$app->formatter->asCurrency(round($data->amount, 2));
                },
                'contentOptions' => ['style' => 'text-align:right'],
                'headerOptions' => ['style' => 'text-align:right'],
            ],
        ],
    ]); ?>
<?php Pjax::end(); ?>
</div>

<script>
        $(document).on('click', '#recurring-payment, #recurring-payment-listing  tbody > tr', function () {
            var recurringPaymentId = $(this).data('key');
            if (!recurringPaymentId) {
            var customUrl = '<?= Url::to(['customer-recurring-payment/recurring-payment-create']); ?>';
            } else {
                var customUrl = '<?= Url::to(['customer-recurring-payment/update']); ?>?id=' + recurringPaymentId;
            }
            $.ajax({
                url    : customUrl,
                type   : 'get',
                dataType: "json",
                data   : $(this).serialize(),
                success: function(response)
                {
                    if(response.status)
                    {
                        $('#popup-modal').modal('show');
                        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Recurring Payments</h4>');
                        $('#modal-content').html(response.data);
                    }
                }
            });
            return false;
        });
</script>