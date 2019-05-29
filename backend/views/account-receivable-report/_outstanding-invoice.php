<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use common\models\Invoice;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use kartik\daterange\DateRangePicker;
use backend\models\search\UserSearch;
use yii\helpers\ArrayHelper;
use common\models\Student;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use common\models\User;

?>
<?php
    LteBox::begin([
        'type' => LteConst::TYPE_DEFAULT,
        'title' => 'Outstanding Invoices',
        'withBorder' => true,
    ])
    ?>

<div class="clearfix"></div>
<div class="grid-row-open">
<?php yii\widgets\Pjax::begin([
    'timeout' => 6000,
    'id' => 'customer-invoice-grid'
]) ?>
<?php echo  GridView::widget([
    'dataProvider' => $outstandingInvoiceDataProvider,
    'options' => ['class' => 'col-md-12'],
    'summary' => false,
    'emptyText' => false,
    'tableOptions' => ['class' => 'table table-bordered table table-condensed'],
    'headerRowOptions' => ['class' => 'bg-light-gray'],
    'rowOptions' => function ($model, $key, $index, $grid) {
        $url = Url::to(['invoice/view', 'id' => $model->id]);

        return ['data-url' => $url];
    },
    'columns' => [
        [
            'label' => 'ID',
            'value' => function ($data) {
                return $data->getInvoiceNumber();
            },
        ],
        [
        'label' => 'Date',
            'value' => function ($data) {
                return !empty($data->date) ? Yii::$app->formatter->asDate($data->date) : null;
            },
        ],
		   [
            'attribute' => 'balance',
            'label' => 'Owing',
            'format' => 'currency',
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right'],
            'enableSorting' => false,
            'value' => function ($data) {
                return (round($data->balance, 2) > 0.00 && round($data->balance, 2) <= 0.09) || (round($data->balance, 2) < 0.00 && round($data->balance, 2) >= -0.09) ? 0.00  : round($data->balance, 2) ;
            },
        ],
    ],
]); ?>
<?php \yii\widgets\Pjax::end(); ?>
</div>
<?php LteBox::end() ?>
	
