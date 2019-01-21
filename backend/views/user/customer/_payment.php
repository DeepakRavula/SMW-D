<?php

use yii\helpers\Url;
use common\components\gridView\KartikGridView;
use yii\helpers\ArrayHelper;
use common\models\PaymentMethod;
use common\models\Location;
use common\models\Payment;
use common\models\User;
use yii\widgets\Pjax;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;

?>

<?php
$locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
$columns = [
	[
		'attribute' => 'number',
		'label' => 'Number',
		'contentOptions' => ['class' => 'text-left', 'style' => 'width:10%'],
		'headerOptions' => ['class' => 'text-left', 'style' => 'width:10%'],
		'value' => function ($data) {
			return $data->getPaymentNumber();
		},
	],
    [
		'contentOptions' => ['class' => 'text-left', 'style' => 'width:20%'],
		'headerOptions' => ['class' => 'text-left', 'style' => 'width:20%'],
		'attribute' => 'dateRange',
		'value' => function ($data) {
			if (!empty($data->date)) {
				$lessonDate = Yii::$app->formatter->asDate($data->date);
				return $lessonDate;
			}
			return null;
		},
    ],
    [
		'contentOptions' => ['class' => 'text-left', 'style' => 'width:15%'],
		'headerOptions' => ['class' => 'text-left', 'style' => 'width:15%'],
		'label' => 'Payment Method',
		'attribute' => 'paymentMethod',
		'value' => function ($data) {
			return $data->paymentMethod->name;
		},
	],
	[
		'label' => 'Notes',
		'attribute' => 'notes',
		'value' => function ($data) {
			return $data->notes;
		},
		'contentOptions' => ['class' => 'text-left', 'style' => 'width:20%'],
		'headerOptions' => ['class' => 'text-left', 'style' => 'width:20%'],
    ],
    [
		'label' => 'Amount',
		'attribute' => 'amount',
		'value' => function ($data) {
			$amount = round($data->amount, 2);
			return Yii::$app->formatter->asCurrency($amount);
		},
		'contentOptions' => ['class' => 'text-right', 'style' => 'width:10%'],
		'headerOptions' => ['class' => 'text-right', 'style' => 'width:10%'],
    ],
];
?>

<?php $boxTools = $this->render('_payment-buttons');	?>
<?php LteBox::begin([
        'type' => LteConst::TYPE_DEFAULT,
        'title' => 'Payments',
		'withBorder' => true,
		'boxTools' => $boxTools,
    ])
    ?>
<div class="clearfix"></div>
<div>
<?php Pjax::begin(['id' => 'customer-payment-listing', 'timeout' => 6000, 'enablePushState' => false]); ?>
    <?= KartikGridView::widget([
		'dataProvider' => $paymentsDataProvider,
		'options' => ['class' => ''],
		'headerRowOptions' => ['class' => 'bg-light-gray'],
		'tableOptions' => ['class' => 'table table-bordered table-responsive table-condensed', 'id' => 'payment'],
		'columns' => $columns,
	]); ?>
<?php Pjax::end(); ?>
</div>
<div class="more-payment pull-right" id = "admin-login" style = "display:none">
    <a class = "show-more" href = "">Show More</a>
</div>
<?php LteBox::end() ?>

<script>
	$(document).ready(function() {
	 var payment_count = '<?= $count; ?>' ;
		if (payment_count > 10) {
			$(".more-payment").show();
			var customer = '<?= $userModel->userProfile->firstname; ?>' ;
			var params = $.param({ 'PaymentSearch[customer]': customer, 'PaymentSearch[isDefault]': 0 });
			var url = '<?= Url::to(['payment/index']); ?>?' + params;
			$('.show-more').attr("href", url);
		}
	}); 

	$(document).on('click', '#customer-payment-listing  tbody > tr', function () {
        var paymentId = $(this).data('key');
		var params = $.param({'PaymentEditForm[paymentId]': paymentId });
        var customUrl = '<?= Url::to(['payment/view']); ?>?' +params;
        $.ajax({
            url: customUrl,
            type: 'get',
            dataType: "json",
            success: function (response)
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