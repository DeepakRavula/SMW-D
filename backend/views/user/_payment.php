<?php

use yii\grid\GridView;
use common\models\Payment;
use common\models\Allocation;
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
<center><b><h4 class="pull-left m-r-20 col-md-12"><?= 'Accounts Receivable Sub-Ledger for ' . $model->publicIdentity ?> </h4></b></center>
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
		'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
        'columns' => [
            [
                'label' => 'Date',
                'value' => function($data) {
					$date = \DateTime::createFromFormat('Y-m-d H:i:s',$data->allocation->date);
                    return ! empty($data->allocation->date) ? $date->format('d M Y') : null;
                },
            ],
			[
                'label' => 'Description',
				'value' => function($data){
					switch($data->allocation->type){
						case Allocation::TYPE_OPENING_BALANCE:
							$description = 'Opening Balance';
						break;
						case Allocation::TYPE_RECEIVABLE:
							$description = 'Payment Received';
						break;
						case Allocation::TYPE_PAID:
							$description = 'Invoice Generated';
						break;
						default:
							$description = null;
					}
					return $description;
				}
            ],
			[
                'label' => 'Debit',
				'value' => function($data){
					if($data->allocation->type === Allocation::TYPE_OPENING_BALANCE || $data->allocation->type === Allocation::TYPE_RECEIVABLE){
						return ! empty($data->allocation->amount) ? Yii::$app->formatter->asCurrency($data->allocation->amount) : null;	
					}
				}
            ],
			[
                'label' => 'Credit',
				'value' => function($data){
					if($data->allocation->type === Allocation::TYPE_PAID){
						return ! empty($data->allocation->amount) ? Yii::$app->formatter->asCurrency($data->allocation->amount) : null;	
					}
				}
            ],
			[
                'label' => 'Balance',
            ],
	    ],
    ]); ?>
<?php \yii\widgets\Pjax::end(); ?>
