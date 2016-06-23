<?php

use yii\grid\GridView;
use common\models\Invoice;
?>
<div class="col-md-12">
	<h4 class="pull-left m-r-20">Payments</h4>
</div>
<?php echo GridView::widget([
        'dataProvider' => $paymentsDataProvider,
        'options' => ['class' => 'col-md-12'],
        'tableOptions' =>['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray' ],
        'rowOptions' => function ($model, $key, $index, $grid) {
            $u= \yii\helpers\StringHelper::basename(get_class($model));
            $u= yii\helpers\Url::toRoute(['/'.strtolower($u).'/view']);
            return ['id' => $model['id'], 'style' => "cursor: pointer", 'onclick' => 'location.href="'.$u.'?id="+(this.id);'];
        },
        'columns' => [
           /* ['class' => 'yii\grid\SerialColumn'],
            [
                'label' => 'Invoice Number',
                'value' => function($data) {
                    return ! empty($data->invoice->invoice_number) ? $data->invoice->invoice_number : null;
                },
            ],
            [
			'label' => 'Payment Method',
				'value' => function($data) {
					return ! empty($data->paymentMethods->name) ? $data->paymentMethods->name : null;
                },
			],*/
            'amount:currency',            
	    ],
    ]); ?>
