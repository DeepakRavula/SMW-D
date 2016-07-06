<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\Invoice;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Invoices';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invoice-index p-10">

<?php echo Html::a('Add Invoice', ['invoice/create'], ['class' => 'btn btn-success m-b-10 m-t-20 pull-right'])?>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' =>['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray' ],
        'rowOptions' => function ($model, $key, $index, $grid) {
            $u= \yii\helpers\StringHelper::basename(get_class($model));
            $u= yii\helpers\Url::toRoute(['/'.strtolower($u).'/view']);
            return ['id' => $model['id'], 'style' => "cursor: pointer", 'onclick' => 'location.href="'.$u.'?id="+(this.id);'];
        },
        'columns' => [
        'invoice_number',
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
                    return ! empty($data->lineItems[0]->lesson->enrolmentScheduleDay->enrolment->student->customer->publicIdentity) ? $data->lineItems[0]->lesson->enrolmentScheduleDay->enrolment->student->customer->publicIdentity : null;
                },
            ],
	    	[
				'label' => 'Status',
				'value' => function($data) {
					switch($data->status){
						case Invoice::STATUS_OWING:
							$status = 'Owing';
						break;
						case Invoice::STATUS_PAID:
							$status = 'Paid';
						break;
						case Invoice::STATUS_CREDIT:
							$status = 'Credited';
						break;
					}
					return $status;
                },
			],
            'total',
        ],
    ]); ?>

</div>
