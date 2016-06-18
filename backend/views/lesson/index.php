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
<div class="lesson-index p-10">
<?php yii\widgets\Pjax::begin(['id' => 'lesson-index']); ?>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' =>['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray' ],
        // 'rowOptions' => function ($model, $key, $index, $grid) {
        //     $u= \yii\helpers\StringHelper::basename(get_class($model));
        //     $u= yii\helpers\Url::toRoute(['/'.strtolower($u).'/view']);
        //     return ['id' => $model['id'], 'style' => "cursor: pointer", 'onclick' => 'location.href="'.$u.'?id="+(this.id);'];
        //},
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
					$lessonDate = \DateTime::createFromFormat('Y-m-d H:i:s', $data->date);
					$currentDate = new \DateTime();

					if($lessonDate <= $currentDate) {
						$status = 'Completed';
					} else {
						$status = 'Scheduled';
					}

					return $status;
                },
			],
			[
				'label' => 'Invoice Status',
				'value' => function($data) {
					$status = null;

					if( ! empty($data->invoiceLineItem->invoice->status)) {
						switch($data->invoiceLineItem->invoice->status){
							case Invoice::STATUS_PAID:
								$status = 'Paid';
							break;
							case Invoice::STATUS_OWING:
								$status = 'Owing';
							break;
							case Invoice::STATUS_CREDIT:
								$status = 'Credit';
							break;
						}
					}
					else {
						$status = 'Not Invoiced';	
					}
					return $status;
                },
			],
			[
				'label' => 'Date',
				'value' => function($data) {
					$date = date("d-m-Y", strtotime($data->date)); 
					return ! empty($date) ? $date : null;
                },
			],
			[
				'class' => 'yii\grid\ActionColumn',
				'template' => '{view} {invoice} {delete}',
				'buttons' => [
					'invoice' => function ($url, $model) {
						return Html::a(
							'<i class="fa fa-file-pdf-o" data-toggle="tooltip" data-placement="bottom" title="Generate Invoice"></i>',
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

