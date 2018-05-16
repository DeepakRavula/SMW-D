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
$boxTools = $this->render('_invoice-buttons', [
    'userModel' => $userModel,
    ]);	
    LteBox::begin([
        'type' => LteConst::TYPE_DEFAULT,
        'title' => 'Invoices',
        'withBorder' => true,
        'boxTools' => $boxTools,
    ])
    ?>

<div class="clearfix"></div>
<div class="grid-row-open">
<?php yii\widgets\Pjax::begin([
    'timeout' => 6000,
    'id' => 'customer-invoice-grid'
]) ?>
<?php echo  GridView::widget([
    'dataProvider' => $invoiceDataProvider,
    'options' => ['class' => 'col-md-12'],
    'summary' => false,
    'emptyText' => false,
    'tableOptions' => ['class' => 'table table-bordered'],
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
            'label' => 'Status',
            'value' => function ($data) {
                return $data->getStatus();
            },
        ],
        [
            'attribute' => 'total',
            'label' => 'Total',
            'format' => 'currency',
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right'],
            'enableSorting' => false,
            'value' => function ($data) {
                return Yii::$app->formatter->asDecimal($data->total);
            },
        ],
    ],
]); ?>
<?php \yii\widgets\Pjax::end(); ?>
</div>
<div class="more-invoice" style = "display:none">
    <a class = "see-more" href = "">seemore</a>
</div>
<?php LteBox::end() ?>
<script>
	$(document).ready(function() {
	 var invoice_count = '<?= $count; ?>' ;
		if(invoice_count > 10) {
			$(".more-invoice").show();
			var dateRange = "";
			var customer = '<?= $userModel->userProfile->firstname; ?>' ;
			var type = <?= Invoice::TYPE_INVOICE; ?>;
			var params = $.param({ 'InvoiceSearch[customer]': customer, 'InvoiceSearch[type]': type, 'InvoiceSearch[invoiceDateRange]': dateRange });
			var url = '<?= Url::to(['invoice/index']); ?>?' + params;
			$('.see-more').attr("href", url);
		}
	});
</script>
	
