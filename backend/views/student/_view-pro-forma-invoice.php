<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;
use common\models\Student;
use common\models\InvoiceLineItem;
?>
<?php
	$results = [];
	$locationId = Yii::$app->session->get('location_id');
	$proFormaInvoiceCredits = Student::find()
		->studentProFormaCredit($locationId, $model->customer->id);
	$proFormaDataProvider = new ActiveDataProvider([
		'query' => $proFormaInvoiceCredits,
	]);
?>
<?php yii\widgets\Pjax::begin(['id' => 'student-listing']); ?>
    <?php echo GridView::widget([
        'dataProvider' => $proFormaDataProvider,
        'tableOptions' =>['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray' ],
        'columns' => [
			[
				'label' => 'Invoice Number',
				'value' => function($data) {
					
                } 
			],
			[
				'label' => 'Date',
				'value' => function($data) {
					
                } 
			],
			[
				'label' => 'Credit',
				'value' => function($data) {
					
                } 
			],
        ],
    ]); ?>

	<?php yii\widgets\Pjax::end(); ?>