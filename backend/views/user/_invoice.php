<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use common\models\Invoice;
use yii\bootstrap\Tabs;
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
            Html::tag('i', ' Add', ['class' => 'fa fa-plus-circle']),
            Url::to(['blank-invoice','Invoice[customer_id]' => $userModel->id, 'Invoice[type]' => INVOICE::TYPE_INVOICE]), [
            'class' => 'btn btn-primary btn-sm',
            ]);
    ?>
</div>
<div class="clearfix"></div>
<hr class="hr-ad right-side-faded">
<div class="grid-row-open">
<?php yii\widgets\Pjax::begin([
	'timeout' => 6000,
]) ?>
<?php echo  GridView::widget([
    'dataProvider' => $invoiceDataProvider,
    'options' => ['class' => 'col-md-12'],
    'tableOptions' =>['class' => 'table table-bordered'],
    'headerRowOptions' => ['class' => 'bg-light-gray' ],
    'rowOptions' => function ($model, $key, $index, $grid) {
        $url = Url::to(['invoice/view', 'id' => $model->id]);
    return ['data-url' => $url];
    },
    'columns' => [
		[
			'label' => 'Invoice Number',
			'value' => function($data) {
				return $data->getInvoiceNumber();
			},
		],
        [
            'label' => 'Student Name',
            'value' => function($data) {
                return ! empty($data->lineItems[0]->lesson->enrolment->student->fullName) ? $data->lineItems[0]->lesson->enrolment->student->fullName. ' (' .$data->lineItems[0]->lesson->enrolment->program->name. ')' : null;
            },
        ],
        [
        'label' => 'Date',
            'value' => function($data) {
                return ! empty($data->date) ? Yii::$app->formatter->asDate($data->date) : null;
            }
        ],
        [
            'label' => 'Status',
            'value' => function($data) {
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
