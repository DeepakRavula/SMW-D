<?php

use kartik\grid\GridView;
use yii\helpers\Url;
use yii\helpers\Html;
use common\components\gridView\KartikGridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['breadcrumbs'][] = $this->title;
?> 
<div class="recurring-payment-index">  
<?php Pjax::begin(['id' => 'recurring-payment-listing']); ?>
<?= KartikGridView::widget([
    'dataProvider' => $dataProvider,
    'summary' => false,
    'emptyText' => false,
    'tableOptions' => ['class' => 'table table-bordered'],
    'headerRowOptions' => ['class' => 'bg-light-gray'],
    'rowOptions' => function ($model, $key, $index, $grid) use ($searchModel) {
        $data = [];
        $class = "";
        if ($model->isExpired()) {
            $class = $class .'highlight-expiry';
        }
        if ($searchModel->showAll) {
            if (!$model->isRecurringPaymentEnabled) {
                $class = $class.' '.'danger inactive';
            } else {
                $class = $class.' '.'info active';
            }

        }
        $data = array_merge($data, ['class' => $class]);
        return $data;
    },
    'columns' => [
            [
                'attribute' => 'customer',
                'value' => function ($data) {
                    return $data->customer->publicIdentity;
                },
            ],
            [
                'label' => 'To Be Entered On',
                'value' => function ($data) {
                    return Yii::$app->formatter->asDate($data->nextEntryDay);
                },
            ],
            [
                'label' => 'Next Payment Date',
                'value' => function ($data) {
                    return Yii::$app->formatter->asDate($data->nextPaymentDate());
                },
            ],
            [
                'label' => 'Frequency',
                'value' => function ($data) {
                    return $data->paymentFrequency->name;
                },
            ],
            [
                'attribute' => 'expiryDate',
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
        'toolbar' =>  [
            [
                'content' =>
                    Html::a('<i class="fa fa-plus"></i>', '#', [
                        'class' => 'btn btn-success', 'id' => 'recurring-payment'
                    ]),
                'options' => ['title' =>'Add',
                              'class' => 'btn-group mr-2']
                ],
            ['content' =>  $this->render('_show-all-button', ['searchModel' => $searchModel]),
            'options' => ['title' =>'Filter',]
               ],
            '{export}',
            '{toggleData}'
        ],
        'panel' => [
            'type' => GridView::TYPE_DEFAULT,
            'heading' => 'Recurring Payments'
        ],
    ]); ?>
<?php Pjax::end(); ?>
</div>

<script>
        $(document).on('click', '#recurring-payment, #recurring-payment-listing  tbody > tr', function () {
            var recurringPaymentId = $(this).data('key');
            if (!recurringPaymentId) {
            var customUrl = '<?= Url::to(['customer-recurring-payment/create']); ?>';
            } else {
                var customUrl = '<?= Url::to(['customer-recurring-payment/update']); ?>?id=' + recurringPaymentId;
                var url = '<?= Url::to(['customer-recurring-payment/delete']); ?>?id=' + recurringPaymentId;
                $('.modal-delete').show();
                $(".modal-delete").attr("action", url);
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
                        var showAll = $(this).is(":checked");
    	                var params = $.param({ 'CustomerRecurringPaymentSearch[showAll]': (showAll | 0) });
      	                var url = "<?php echo Url::to(['customer-recurring-payment/index']); ?>?"+params;
                        $.pjax.reload({url: url, container: "#recurring-payment-listing", replace: false, timeout: 4000});
                    }
                }
            });
            return false;
        });

    $(document).off('change', '#customerrecurringpaymentsearch-showall').on('change', '#customerrecurringpaymentsearch-showall', function () {
      	var showAll = $(this).is(":checked");
    	var params = $.param({ 'CustomerRecurringPaymentSearch[showAll]': (showAll | 0) });
      	var url = "<?php echo Url::to(['customer-recurring-payment/index']); ?>?"+params;
        $.pjax.reload({url: url, container: "#recurring-payment-listing", replace: false, timeout: 4000});  //Reload GridView
    });
    $(document).on('modal-delete', function(event, params) {
        $.pjax.reload({container: "#recurring-payment-listing", replace: false, timeout: 4000});
        return false;
    });
    $(document).on('modal-success', function(event, params) {
        $.pjax.reload({container: "#recurring-payment-listing", replace: false, timeout: 4000});
        return false;
    });
</script>