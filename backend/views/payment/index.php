<?php

use yii\helpers\Url;
use common\components\gridView\KartikGridView;
use yii\helpers\ArrayHelper;
use common\models\PaymentMethod;
use common\models\Location;
use common\models\Payment;
use common\models\User;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Payments';
$this->params['action-button'] = $this->render('_action-button');
?>
<?php Pjax::begin(['id' => 'payment-listing']); ?>

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
		'filterType' => KartikGridView::FILTER_SELECT2,
		'filter' => ArrayHelper::map(Payment::find()
			->notDeleted()
			->location($locationId)
			->exceptAutoPayments()
			->andWhere(['between', 'DATE(payment.date)',
		            (new \DateTime($searchModel->startDate))->format('Y-m-d'),
					(new \DateTime($searchModel->endDate))->format('Y-m-d')])
			->orderBy(['payment.id' => SORT_ASC])
			->all(), 'id', 'paymentNumber'),
		'filterWidgetOptions' => [
			'options' => [
				'id' => 'payment-number'
			],
			'pluginOptions' => [
				'allowClear' => true
			]
		],
		'filterInputOptions' => ['placeholder' => 'Number']
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
		'filterType' => KartikGridView::FILTER_DATE_RANGE,
		'filterWidgetOptions' => [
			'id' => 'enrolment-startdate-search',
			'convertFormat' => true,
			'initRangeExpr' => true,
			'options' => [
				'readOnly' => true,
			],
			'pluginOptions' => [
			'autoApply' => true,
			'allowClear' => true,
			'ranges' => [
				Yii::t('kvdrp', 'Last Month') => ["moment().subtract(1, 'month').startOf('month')",
				"moment().subtract(1, 'month').endOf('month')"],
				Yii::t('kvdrp', 'Last Week') => ["moment().subtract(1, 'week').startOf('week')",
				"moment().subtract(1, 'week').endOf('week')"],
				Yii::t('kvdrp', "Yesterday") => ["moment().startOf('day').subtract(1,'days')", "moment().endOf('day').subtract(1,'days')"],
				Yii::t('kvdrp', "Today") => ["moment().startOf('day')", "moment()"],
				Yii::t('kvdrp', 'This Week') => ["moment().startOf('week')",
				"moment().endOf('week')"],
				Yii::t('kvdrp', 'This Month') => ["moment().startOf('month')",
				"moment().endOf('month')"],
			],
			'locale' => [
				'format' => 'M d, Y',
			],
			'opens' => 'right',
			],
		],
    ],
    [
		'label' => 'Customer',
		'attribute' => 'customer',
		'value' => function ($data) {
			return $data->user->publicIdentity ?? null;
		},
		'contentOptions' => ['class' => 'text-left', 'style' => 'width:25%'],
		'headerOptions' => ['class' => 'text-left', 'style' => 'width:25%'],
    ],
    [
		'contentOptions' => ['class' => 'text-left', 'style' => 'width:15%'],
		'headerOptions' => ['class' => 'text-left', 'style' => 'width:15%'],
		'label' => 'Payment Method',
		'attribute' => 'paymentMethod',
		'value' => function ($data) {
			return $data->paymentMethod->name;
		},
		'filterType' => KartikGridView::FILTER_SELECT2,
		'filter' => ArrayHelper::map(PaymentMethod::find()
				->andWhere(['displayed' => true])
				->orderBy(['name' => SORT_ASC])
				->asArray()->all(), 'name', 'name'),
		'filterWidgetOptions' => [
			'pluginOptions' => [
				'allowClear' => true
			],
		],
		'filterInputOptions' => ['placeholder' => 'Payment Method']
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
		'label' => 'Reference',
		'attribute' => 'reference',
		'value' => function ($data) {
			return $data->reference;
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

<div>
    <?= KartikGridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'filterUrl' => Url::to(['payment/index', 'PaymentSearch[isDefault]' => false]),
		'summary' => "Showing {begin} - {end} of {totalCount} items",
		'options' => ['class' => ''],
		'headerRowOptions' => ['class' => 'bg-light-gray'],
		'tableOptions' => ['class' => 'table table-bordered table-responsive table-condensed', 'id' => 'payment'],
		'columns' => $columns,
	]); ?>
</div>
<?php Pjax::end(); ?>

<script>
    $(document).on('click', '#payment-listing  tbody > tr', function () {
        var paymentId = $(this).data('key');
		var params = $.param({'PaymentEditForm[paymentId]': paymentId });
        var customUrl = '<?= Url::to(['payment/view']); ?>?' + params;
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

	$(document).on('modal-success', function(event, params) {
        $.pjax.reload({container: "#payment-listing", replace: false, timeout: 4000});
        return false;
    });
	$(document).on('modal-delete', function(event, params) {
        $.pjax.reload({container: "#payment-listing", replace: false, timeout: 4000});
        return false;
    });
</script>
