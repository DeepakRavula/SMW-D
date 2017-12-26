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
?>
<div class="col-md-12">
	<?= Html::a('<i title="Add" class="fa fa-plus-circle"></i>',
        ['invoice/blank-invoice', 'Invoice[customer_id]' => $userModel->id,
            'Invoice[type]' => INVOICE::TYPE_INVOICE, ], [
            'class' => 'add-new-invoice text-add-new m-r-10',
        ]); ?>
	<?= Html::a('<i title="Print" class="fa fa-print"></i>', ['print/customer-invoice', 'id' => $userModel->id], ['id' => 'invoice-print', 'class' => 'text-add-new', 'target' => '_blank']) ?>
	<?php $form = ActiveForm::begin([
		'id' => 'customer-invoice-search-form'
    ]); ?>
	<div class="col-xs-3">
    <?php 
   echo DateRangePicker::widget([
    'model' => $userModel,
    'attribute' => 'dateRange',
    'convertFormat' => true,
    'initRangeExpr' => true,
    'pluginOptions' => [
        'autoApply' => true,
        'ranges' => [
            Yii::t('kvdrp', 'This Month') => ["moment().startOf('month')", "moment().endOf('month')"],
            Yii::t('kvdrp', 'Last Month') => ["moment().subtract(1, 'month').startOf('month')", "moment().subtract(1, 'month').endOf('month')"],
	    	Yii::t('kvdrp', 'This Year') => ["moment().startOf('year')", "moment().endOf('year')"],
            Yii::t('kvdrp', 'Last Year') => ["moment().subtract(1, 'year').startOf('year')", "moment().subtract(1, 'year').endOf('year')"],
        ],
			
        'locale' => [
            'format' => 'M d,Y',
        ],
        'opens' => 'right',
        ],

    ]);
   ?>
    </div>
	<div class="col-xs-2">
        <?php echo $form->field($userModel, 'invoiceStatus')->dropDownList(UserSearch::invoiceStatuses())->label('Invoice Status')->label(false); ?>
    </div>
	<div class="col-xs-3">
		<?php
		$locationId = \common\models\Location::findOne(['slug' => \Yii::$app->location])->id;
		$students = ArrayHelper::map(Student::find()
                        ->notDeleted()->orderBy(['first_name' => SORT_ASC])
			->joinWith(['customer' => function($query) use($userModel) {
				$query->andWhere(['user.id' => $userModel->id]);
			}])
			->location($locationId)
			->all(), 'id', 'fullName'); ?>
        <?php echo $form->field($userModel, 'studentId')->dropDownList($students, ['prompt' => 'Select Student'])->label(false); ?>
    </div>
    <div class="col-md-2 form-group M-t-5">
	<?php echo Html::submitButton(Yii::t('backend', 'Search'), ['id' => 'search', 'class' => 'btn btn-primary btn-sm']) ?>
		
    </div>	
	<?php ActiveForm::end(); ?>
	
</div>
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
            'label' => 'Invoice Number',
            'value' => function ($data) {
                return $data->getInvoiceNumber();
            },
        ],
        [
            'label' => 'Student Name',
            'value' => function ($data) {
                return $data->getStudentProgramName();
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
<script>
$(document).ready(function(){
	$("#customer-invoice-search-form").on("submit", function() {
		var dateRange = $('#user-daterange').val();
		var invoiceStatus = $('#user-invoicestatus').val();
    	//$("#user-studentid").on("change", function() {
			var studentId = $("#user-studentid").val();	
		//});
		$.pjax.reload({container:"#customer-invoice-grid", replace:false, timeout:6000, data:$(this).serialize()});
		var params = $.param({ 'User[dateRange]': dateRange, 'User[invoiceStatus]': invoiceStatus, 'User[studentId]': studentId });
		var url = '<?= Url::to(['print/customer-invoice', 'id' => $userModel->id]); ?>&' + params;
		$('#invoice-print').attr('href', url);
		return false;
    });
});
</script>