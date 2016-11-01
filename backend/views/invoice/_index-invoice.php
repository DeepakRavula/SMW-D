<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use common\models\Invoice;
use backend\models\search\InvoiceSearch;
use yii\widgets\ActiveForm;
use kartik\switchinput\SwitchInput;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel backend\models\search\InvoiceSearch */

$proFormaAddButton = Html::a('<i class="fa fa-plus-circle" aria-hidden="true"></i> Add', ['invoice/create', 'Invoice[type]' => $searchModel->type], ['class' => 'btn btn-primary btn-sm']); 
$invoiceAddButton = Html::a('<i class="fa fa-plus-circle" aria-hidden="true"></i> Add', ['invoice/blank-invoice',], ['class' => 'btn btn-primary btn-sm']); 

$actionButton = (int) $searchModel->type === InvoiceSearch::TYPE_PRO_FORMA_INVOICE ? $proFormaAddButton : $invoiceAddButton ;

$this->title = (int) $searchModel->type === InvoiceSearch::TYPE_PRO_FORMA_INVOICE ? 'Pro-forma Invoices' : 'Invoices';
$this->params['action-button'] = $actionButton; 
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invoice-index p-10">
	<?php if((int) $searchModel->type === InvoiceSearch::TYPE_PRO_FORMA_INVOICE) : ?>
	<div>
	<?php $form			 = ActiveForm::begin(); ?>
			<?=
			$form->field($searchModel, 'notSent')->widget(SwitchInput::classname(),
				[
				'pluginOptions' => [
					'onText'=>'Not Sent',
			        'offText'=>'All',
				]
			])->label(false);
			?>
		<?php ActiveForm::end(); ?>
	</div>
	<?php endif; ?>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
	<?php $columns = [
			[
			'label' => 'Invoice Number',
				'value' => function($data) {
					return $data->getInvoiceNumber();
                },
			],
            [
			'label' => 'Date',
				'value' => function($data) {
					$date = Yii::$app->formatter->asDate($data->date); 
					return ! empty($date) ? $date : null;
                },
			],
			[
			    'label' => 'Customer',
                'value' => function($data) {
                    return ! empty($data->user->publicIdentity) ? $data->user->publicIdentity : null;
                },
            ],
			[
			    'label' => 'Phone',
                'value' => function($data) {
                    return ! empty($data->user->phoneNumber->number) ? $data->user->phoneNumber->number : null;
                },
            ],
	    	[
				'label' => 'Status',
				'value' => function($data) {
					$status = null;
					if((int) $data->type === Invoice::TYPE_PRO_FORMA_INVOICE){
						$status = 'None';
					}else{
						$status = $data->getStatus();
					}
					return $status;
                },
				'contentOptions' => function($data) {
                    $options = [];
                    $type = (int) $data->type === Invoice::TYPE_INVOICE ? 'invoice' : 'pro-forma-invoice';
                    Html::addCssClass($options, $type . '-' . strtolower($data->getStatus()));                    
                    return $options;
                },
			],
			[
				'value' => function($data) {
					if((int) $data->type === Invoice::TYPE_INVOICE){
						if($data->status === 'Paid'){
							return $data->total;
						}else{	
							return $data->invoiceBalance;
						}
                	}
				},
				'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => function($data) {
                    $options = [];
                    $type = (int) $data->type === Invoice::TYPE_INVOICE ? 'invoice' : 'pro-forma-invoice';
                    Html::addCssClass($options, 'text-right');
                    Html::addCssClass($options, $type . '-' . strtolower($data->getStatus()));                    
                    return $options;
                },
            	'enableSorting' => false,
			],
			[
				'label' => 'Total',
				'value' => function($data) {
						return $data->total;
                },
				'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
				'enableSorting' => false,
            ]
        ];

		
		?>
	<?php yii\widgets\Pjax::begin([
		'id' => 'invoice-listing',
	]) ?>
    <div class="grid-row-open">
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' =>['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray' ],
        'rowOptions' => function ($model, $key, $index, $grid) {
            $url =  Url::to(['invoice/view', 'id' => $model->id]);
        return ['data-url' => $url];
        },
        'columns' => $columns,
    ]); ?>
	<?php \yii\widgets\Pjax::end(); ?>
    </div>
</div>
<script>
$(document).ready(function() {
	$('input[name="InvoiceSearch[notSent]"]').on('switchChange.bootstrapSwitch', function(event, state) {
		var url = "<?php echo Url::to(['invoice/index']);?>?InvoiceSearch[notSent]=" + (state | 0) + '&InvoiceSearch[type]=' + "<?php echo $searchModel->type;?>";
      $.pjax.reload({url:url,container:"#invoice-listing",replace:false,  timeout: 4000});
  });
});
</script>