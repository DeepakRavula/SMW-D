<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\Invoice;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Invoices';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invoice-index">


    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
			[
				'label' => 'Program Name',
				'value' => function($data) {
					return ! empty($data->lesson->enrolmentScheduleDay->enrolment->qualification->program->name) ? $data->lesson->enrolmentScheduleDay->enrolment->qualification->program->name : null;
                },
			],
            'amount',
			[
				'label' => 'Date',
				'value' => function($data) {
					$date = date("d-m-y", strtotime($data->date)); 
					return ! empty($date) ? $date : null;
                },
			],
	    	[
				'label' => 'Status',
				'value' => function($data) {
					switch($data->status){
						case Invoice::STATUS_UNPAID:
							$status = 'Unpaid';
						break;
						case Invoice::STATUS_PAID:
							$status = 'Paid';
						break;
						case Invoice::STATUS_CANCELED:
							$status = 'Canceled';
						break;
					}
					return $status;
                },
			],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
