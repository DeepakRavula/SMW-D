<?php

use yii\grid\GridView;
use common\models\Payment;
?>
<div class="col-md-12">
	<h4 class="pull-left m-r-20">Payments </h4> 
	<a href="#" class="add-new-payment text-add-new"><i class="fa fa-plus"></i></a>
	<div class="clearfix"></div>
</div>
<div class="dn show-create-payment-form">
	<?php echo $this->render('_form-payment', [
		'model' => new Payment(),
	]) ?>
</div>

<?php yii\widgets\Pjax::begin() ?>
<?php echo GridView::widget([
        'dataProvider' => $paymentDataProvider,
        'options' => ['class' => 'col-md-12'],
        'tableOptions' =>['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray' ],
        'rowOptions' => function ($model, $key, $index, $grid) {
            $u= \yii\helpers\StringHelper::basename(get_class($model));
            $u= yii\helpers\Url::toRoute(['/'.strtolower($u).'/view']);
            return ['id' => $model['id'], 'style' => "cursor: pointer", 'onclick' => 'location.href="'.$u.'?id="+(this.id);'];
        },
        'columns' => [
            [
                'label' => 'Invoice Number',
                'value' => function($data) {
                    return ! empty($data->invoice_id) ? $data->invoice_id : null;
                },
            ],
			[
                'label' => 'Payment Method',
                'value' => function($data) {
                    return ! empty($data->payment->paymentMethod->name) ? $data->payment->paymentMethod->name : null;
                },
            ],
			'date:date',
            'amount:currency',            
	    ],
    ]); ?>
<?php \yii\widgets\Pjax::end(); ?>
