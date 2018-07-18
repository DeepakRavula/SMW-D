<?php

use yii\helpers\Html;
use common\components\gridView\KartikGridView;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use common\models\Location;
use common\models\User;
use common\models\ProformaInvoice;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\PrivateLessonSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Payment Requests';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php Pjax::begin([
    'timeout' => 6000,
    'enablePushState' => false,
    'id' => 'proforma-invoice-listing',]); ?>
<div class="grid-row-open">
    <?php $locationId = Location::findOne(['slug' => \Yii::$app->location])->id; ?>
    <?php echo KartikGridView::widget([
        'dataProvider' => $dataProvider,
        'id' => 'proforma-invoice-grid',
        'filterModel' => $searchModel,
        'rowOptions' => function ($model, $key, $index, $grid) {
            $url = Url::to(['proforma-invoice/view', 'id' => $model->id]);

            return ['data-url' => $url];
        },
        'summary' => false,
        'emptyText' => false,
        'columns' => [
            [
                'attribute' => 'number',
                'label' => 'Number',
                'contentOptions' => ['style' => 'width:100px'],
                'value' => function ($data) {
                    return $data->getProformaInvoiceNumber();
                },
			'filterType'=>KartikGridView::FILTER_SELECT2,
                'filter'=>ArrayHelper::map(ProformaInvoice::find()
			->location($locationId)->orderBy(['proforma_invoice_number' => SORT_ASC])
                ->all(), 'id', 'proFormaInvoiceNumber'),
                'filterWidgetOptions'=>[
            'options' => [
                'id' => 'proformainvoice',
            ],
                    'pluginOptions'=>[
                        'allowClear'=>true,
            ],
        ],
                'filterInputOptions'=>['placeholder'=>'Number'],
            ],

            [
                'attribute' => 'customer',
                'label' => 'Customer',
                'value' => function ($data) {
                    return !empty($data->user->publicIdentity) ? $data->user->publicIdentity : null;
                },
    'filterType'=> KartikGridView::FILTER_SELECT2,
            'filter' => ArrayHelper::map(User::find()
			    ->customers($locationId)
			    ->joinWith(['userProfile' => function ($query) {
					$query->orderBy('firstname');
				}])
			    ->all(), 'id', 'publicIdentity'),
	    'filterWidgetOptions'=>[
        'options' => [
            'id' => 'customer',
        ],
                'pluginOptions'=>[
                    'allowClear'=>true,
        ],

    ],
            'filterInputOptions'=>['placeholder'=>'Customer'],
            ],
            [
                'attribute' => 'phone',
                'label' => 'Phone',
                'value' => function ($data) {
                    return !empty($data->user->phoneNumber->number) ? $data->user->phoneNumber->number : null;
                },
            ],
            [
                'label' => 'Total',
		'headerOptions' => ['class' => 'text-right'],
	        'contentOptions' => ['class' => 'text-right'],
                'value' => function ($data) {
                    return !empty($data->total) ? Yii::$app->formatter->asCurrency($data->total) : null;
                },
            ],
        ],
    ]); ?>
</div>
    <?php Pjax::end(); ?>

