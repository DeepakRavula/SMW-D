<?php

use yii\helpers\Url;
use common\components\gridView\KartikGridView;
use yii\helpers\ArrayHelper;
use common\models\Location;
use common\models\User;
use common\models\PaymentMethod;
use common\models\PaymentPreference;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Payment Preferences';
?>
<?php Pjax::begin(['id' => 'payment-preference-listing']); ?>

<?php
$locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
$columns = [
    [
		'label' => 'Customer',
		'attribute' => 'customer',
		'value' => function ($data) {
			return $data->publicIdentity ?? null;
		},
		'contentOptions' => ['class' => 'text-left', 'style' => 'width:25%'],
		'headerOptions' => ['class' => 'text-left', 'style' => 'width:25%'],
		'filterType' => KartikGridView::FILTER_SELECT2,
		'filter' => ArrayHelper::map(User::find()->customers($locationId)->notDeleted()->active()
				->all(), 'id', 'publicIdentity'),
		'filterWidgetOptions' => [
			'pluginOptions' => [
			'allowClear' => true,
			],
		],
		'filterInputOptions' => ['placeholder' => 'Customer']
	],
	[
		'label' => 'Day of the Month',
		'attribute' => 'day',
		'value' => function ($data) {
			return $data->customerPaymentPreference->dayOfMonth ?? null;
		},
		'contentOptions' => ['class' => 'text-left', 'style' => 'width:25%'],
		'headerOptions' => ['class' => 'text-left', 'style' => 'width:25%'],
	],
	[
		'label' => 'Payment Method',
		'attribute' => 'paymentMethod',
		'value' => function ($data) {
			return $data->customerPaymentPreference->getPaymentMethodName() ?? null;
		},
		'contentOptions' => ['class' => 'text-left', 'style' => 'width:25%'],
		'headerOptions' => ['class' => 'text-left', 'style' => 'width:25%'],
    ],
    
];
?>

<div>
    <?= KartikGridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'options' => ['class' => ''],
		'headerRowOptions' => ['class' => 'bg-light-gray'],
		'tableOptions' => ['class' => 'table table-bordered table-responsive table-condensed', 'id' => 'payment'],
		'columns' => $columns,
	]); ?>
</div>
<?php Pjax::end(); ?>
