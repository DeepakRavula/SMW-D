<?php
use yii\grid\GridView;
use common\models\Payment;
use common\models\Invoice;
use yii\data\ActiveDataProvider;
use common\models\PaymentMethod;
?>
<?php yii\widgets\Pjax::begin() ?>
<?php echo GridView::widget([
        'dataProvider' => $openingBalanceDataProvider,
        'options' => ['class' => 'col-md-12'],
        'tableOptions' =>['class' => 'table table-bordered m-0'],
        'headerRowOptions' => ['class' => 'bg-light-gray' ],
		'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
        'columns' => [
            [
                'label' => 'Date',
                'value' => function($data) {
					$date = \DateTime::createFromFormat('Y-m-d H:i:s',$data->date);
                    return ! empty($data->date) ? $date->format('d M Y') : null;
                },
            ],
			[
                'label' => 'Payment Method',
                'value' => function($data) {
                    return	'Credit Used'; 
				}
            ],
			[
                'label' => 'Amount',
                'value' => function($data) {
                    	return ! empty($data->amount) ? abs($data->amount) : null;
                },
            ],
	    ],
    ]); ?>
<?php \yii\widgets\Pjax::end(); ?>
