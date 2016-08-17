<?php

use yii\grid\GridView;
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
		'rowOptions' => function ($model, $key, $index, $grid) {
            $u= \yii\helpers\StringHelper::basename(get_class($model));
            $u= yii\helpers\Url::toRoute(['/'.strtolower($u).'/view']);
            return ['id' => $model['id'], 'style' => "cursor: pointer", 'onclick' => 'location.href="'.$u.'?id="+(this.id);'];
        },
        'tableOptions' =>['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray' ],
        'columns' => [
			[
				'label' => 'Student Name',
				'value' => function($data) {
					return ! empty($data->enrolment->student->fullName) ? $data->enrolment->student->fullName : null;
                },
			],
			[
				'label' => 'Program Name',
				'value' => function($data) {
					return ! empty($data->enrolment->program->name) ? $data->enrolment->program->name : null;
                },
			],
			[
				'label' => 'Date',
				'value' => function($data) {
					$date = Yii::$app->formatter->asDate($data->date); 
					return ! empty($date) ? $date : null;
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
        ],
    ]); ?>
	<?php yii\widgets\Pjax::end(); ?>

</div>

