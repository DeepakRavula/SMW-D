<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\Lesson;
use common\models\Invoice;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lessons';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lesson-index">

<?php yii\widgets\Pjax::begin(['id' => 'lesson-index']); ?>
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
			[
				'label' => 'Student Name',
				'value' => function($data) {
					return ! empty($data->enrolmentScheduleDay->enrolment->student->fullName) ? $data->enrolmentScheduleDay->enrolment->student->fullName : null;
                },
			],
			[
				'label' => 'Program Name',
				'value' => function($data) {
					return ! empty($data->enrolmentScheduleDay->enrolment->qualification->program->name) ? $data->enrolmentScheduleDay->enrolment->qualification->program->name : null;
                },
			],	
			[
				'label' => 'Lesson Status',
				'value' => function($data) {
					$status = null;
					switch($data->status){
						case Lesson::STATUS_COMPLETED:
							$status = 'Completed';
						break;
						case Lesson::STATUS_PENDING:
							$status = 'Pending';
						break;
						case Lesson::STATUS_CANCELED:
							$status = 'Canceled';
						break;
					}
					return $status;
                },
			],
			[
				'label' => 'Invoice Status',
				'value' => function($data) {
					$status = null;

					if( ! empty($data->invoice->status)) {
						switch($data->invoice->status){
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
					}
					else {
						$status = 'UnInvoiced';	
					}
					return $status;
                },
			],
			[
				'label' => 'Date',
				'value' => function($data) {
					$date = date("d-m-y", strtotime($data->date)); 
					return ! empty($date) ? $date : null;
                },
			],
			[
				'class' => 'yii\grid\ActionColumn',
				'template' => '{invoice} {view} {update} {delete}',
				'buttons' => [
					'invoice' => function ($url, $model) {
						return Html::a(
							'<span class="glyphicon glyphicon-usd"></span>',
							$url, 
							[
								'title' => 'Generate Invoice',
								'data-pjax' => 'lesson-index',
							]
						);
					},
				],
			],
        ],
    ]); ?>
	<?php yii\widgets\Pjax::end(); ?>

</div>

<?php
$this->registerJs(
   '$("document").ready(function(){ 
        $("#new_medicine").on("pjax:end", function() {
            $.pjax.reload({container:"#medicine"});  //Reload GridView
        });
    });'
);
?>
