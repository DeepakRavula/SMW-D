<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use common\models\Invoice;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;

?>
<style>
    hr{
        margin: 10px 0;
    }
</style>
<div class="col-md-12">
    <h4 class="pull-left m-r-20">Invoices</h4>
	<?php
        echo Html::a(
            Html::tag('i', '', ['class' => 'fa fa-plus-circle']),
            Url::to(['invoice/blank-invoice', 'Invoice[customer_id]' => $userModel->id,
            'Invoice[type]' => INVOICE::TYPE_INVOICE, ]), [
            'class' => 'add-new-invoice text-add-new',
            ]);
    ?>
	<?php $form = ActiveForm::begin([
		'id' => 'customer-invoice-search-form'
    ]); ?>
	<div class="col-md-3">
 <?php
		echo $form->field($userModel, 'fromDate')->widget(DatePicker::classname(), [
		'type' => DatePicker::TYPE_COMPONENT_APPEND,
		'pluginOptions' => [
			'startView' => 3,
			'minViewMode' => 2,
			'autoclose' => true,
			'format' => 'yyyy',
		],
	  ])->label('Date');
	?>
    </div>
    <div class="col-md-3 form-group m-t-5">
        <br>
	<?php echo Html::submitButton(Yii::t('backend', 'Search'), ['id' => 'search', 'class' => 'btn btn-primary']) ?>
    </div>
	<?php ActiveForm::end(); ?>
	<?= Html::a('<i class="fa fa-print"></i> Print', ['invoice-print', 'id' => $userModel->id], ['id' => 'invoice-print', 'class' => 'btn btn-default btn-sm pull-right m-r-10', 'target' => '_blank']) ?>
</div>
<div class="clearfix"></div>
<hr class="hr-ad right-side-faded">
<div class="grid-row-open">
<?php yii\widgets\Pjax::begin([
    'timeout' => 6000,
	'id' => 'customer-invoice-grid'
]) ?>
<?php echo  GridView::widget([
    'dataProvider' => $invoiceDataProvider,
    'options' => ['class' => 'col-md-12'],
    'tableOptions' => ['class' => 'table table-bordered'],
    'headerRowOptions' => ['class' => 'bg-light-gray'],
    'rowOptions' => function ($model, $key, $index, $grid) {
        $url = Url::to(['invoice/view', 'id' => $model->id]);

        return ['data-url' => $url];
    },
    'columns' => [
        [
            'label' => 'Invoice Number',
            'value' => function ($data) {
                return $data->getInvoiceNumber();
            },
        ],
        [
            'label' => 'Student Name',
            'value' => function ($data) {
                return !empty($data->lineItems[0]->lesson->enrolment->student->fullName) ? $data->lineItems[0]->lesson->enrolment->student->fullName.' ('.$data->lineItems[0]->lesson->enrolment->program->name.')' : null;
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
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right'],
            'enableSorting' => false,
        ],
    ],
]); ?>
<?php \yii\widgets\Pjax::end(); ?>
</div>
<script>
$(document).ready(function(){
	$("#customer-invoice-search-form").on("submit", function() {
		var fromDate = $('#user-fromdate').val();
		$.pjax.reload({container:"#customer-invoice-grid", replace:false, timeout:6000, data:$(this).serialize()});
		var url = "<?= Url::to(['user/invoice-print', 'id' => $userModel->id]); ?>&User[fromDate]=" + fromDate;
		$('#invoice-print').attr('href', url);
		return false;
    });
});
</script>