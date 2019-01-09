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
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

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
<?php Pjax::begin(['id' => 'customer-payment-listing', 'timeout' => 6000, 'enablePushState' => false]); ?>
    <?= KartikGridView::widget([
		'dataProvider' => $paymentDataProvider,
		'options' => ['class' => ''],
		'headerRowOptions' => ['class' => 'bg-light-gray'],
		'tableOptions' => ['class' => 'table table-bordered table-responsive table-condensed', 'id' => 'payment'],
		'columns' => $columns,
	]); ?>
<?php Pjax::end(); ?>