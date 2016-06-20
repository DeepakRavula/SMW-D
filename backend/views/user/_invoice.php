<?php

use yii\grid\GridView;
use common\models\Invoice;
?>
<div class="col-md-12">
	<h4 class="pull-left m-r-20">Invoices</h4>
</div>
<?php echo GridView::widget([
        'dataProvider' => $invoiceDataProvider,
        'tableOptions' =>['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray' ],
        'rowOptions' => function ($model, $key, $index, $grid) {
            $u= \yii\helpers\StringHelper::basename(get_class($model));
            $u= yii\helpers\Url::toRoute(['/'.strtolower($u).'/view']);
            return ['id' => $model['id'], 'style' => "cursor: pointer", 'onclick' => 'location.href="'.$u.'?id="+(this.id);'];
        },
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label' => 'Student Name',
                'value' => function($data) {
                    return ! empty($data->lineItems[0]->lesson->enrolmentScheduleDay->enrolment->student->fullName) ? $data->lineItems[0]->lesson->enrolmentScheduleDay->enrolment->student->fullName. ' (' .$data->lineItems[0]->lesson->enrolmentScheduleDay->enrolment->qualification->program->name. ')' : null;
                },
            ],
			'tax:currency',
			'subTotal:currency',
            'total:currency',
			[
			'label' => 'Date',
				'value' => function($data) {
					$date = date("d-m-Y", strtotime($data->date)); 
					return ! empty($date) ? $date : null;
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
            ['class' => 'yii\grid\ActionColumn']
        ],
    ]); ?>
