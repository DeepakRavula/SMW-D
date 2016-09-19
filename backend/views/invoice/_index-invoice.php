<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\Invoice;
use backend\models\search\InvoiceSearch;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel backend\models\search\InvoiceSearch */

$this->title = (int) $searchModel->type === InvoiceSearch::TYPE_PRO_FORMA_INVOICE ? 'Pro-forma Invoices' : 'Invoices';
$this->params['action-button'] = (int) $searchModel->type === InvoiceSearch::TYPE_PRO_FORMA_INVOICE ? Html::a('<i class="fa fa-plus-circle" aria-hidden="true"></i> Add', ['invoice/create', 'Invoice[type]' => $searchModel->type], ['class' => 'btn btn-primary btn-sm']) : Html::a('<i class="fa fa-plus-circle" aria-hidden="true"></i> Add', ['invoice/blank-invoice',], ['class' => 'btn btn-primary btn-sm']); 
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invoice-index p-10">
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
                'contentOptions' => ['class' => 'text-right'],
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
	<?php yii\widgets\Pjax::begin() ?>
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' =>['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray' ],
        'rowOptions' => function ($model, $key, $index, $grid) {
            $u= \yii\helpers\StringHelper::basename(get_class($model));
            $u= yii\helpers\Url::toRoute(['/'.strtolower($u).'/view']);
            return ['id' => $model['id'], 'style' => "cursor: pointer", 'onclick' => 'location.href="'.$u.'?id="+(this.id);'];
        },
        'columns' => $columns,
    ]); ?>
	<?php \yii\widgets\Pjax::end(); ?>

</div>
