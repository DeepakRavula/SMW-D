<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use common\models\Invoice;
use common\models\InvoiceLineItem;
?>
<?php
	$results = [];
	$locationId = Yii::$app->session->get('location_id');
	$proFormaInvoiceCredits = Invoice::find()->alias('i')
		->select(['i.invoice_number', 'i.date', 'SUM(p.amount) as credit'])
		->joinWith(['user' => function($query) use($model){
			$query->joinWith('student s')
				->where(['s.id' => $model->id]);
			}])
		->joinWith(['invoicePayments ip' => function($query){
			$query->joinWith(['payment p' => function($query){
			}]);
			$query->where(['not', ['ip.id' => null]]);
		}])
		->where(['i.type' => Invoice::TYPE_PRO_FORMA_INVOICE])
		->groupBy('i.id');
	$proFormaDataProvider = new ActiveDataProvider([
		'query' => $proFormaInvoiceCredits,
	]);
?>
<?php yii\widgets\Pjax::begin(); ?>
    <?php echo GridView::widget([
        'dataProvider' => $proFormaDataProvider,
        'tableOptions' =>['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray' ],
        'columns' => [
			[
				'label' => 'Invoice Number',
				'value' => function($data) {
					return $data->getInvoiceNumber();	
                } 
			],
			[
				'label' => 'Date',
				'value' => function($data) {
					return Yii::$app->formatter->asDate($data->date);	
                } 
			],
			[
				'label' => 'Credit',
				'value' => function($data) {
					return $data->credit;	
                } 
			],
        ],
    ]); ?>

	<?php yii\widgets\Pjax::end(); ?>