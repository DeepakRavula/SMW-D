<?php

use yii\grid\GridView;
use yii\helpers\Url;
use common\models\Invoice;
use common\models\Lesson;
use common\models\PrivateLesson;
?>
<div class="private-lesson-index p-10">
<?php yii\widgets\Pjax::begin(['id' => 'lesson-index']); ?>
    <?php $columns = [
			[
				'label' => 'Student Name',
				'value' => function($data) {
					return ! empty($data->course->enrolment->student->fullName) ? $data->course->enrolment->student->fullName : null;
					}
			],
			[
				'label' => 'Program Name',
				'value' => function($data) {
					return ! empty($data->course->program->name) ? $data->course->program->name : null;
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
				'label' => 'Expiry Date',
				'value' => function($data) {
					if (!empty($data->privateLesson->expiryDate)) {
						$date = Yii::$app->formatter->asDate($data->privateLesson->expiryDate);
					}
					return ! empty($date) ? $date : null;
                },
			],            
			[
				'label' => 'Status',
				'value' => function($data) {
					$status = null;
					if (!empty($data->status)) {
					return $data->getStatus();
					}
				return $status;
                },
			],
			[
				'label' => 'Invoiced ?',
				'value' => function($data) {
					$status = null;
				if (!empty($data->invoice->status)) {
					$status = 'Yes'; 
				} else {
					$status = 'No';
				}
				return $status;
			},
			],
        ];
            
    ?>
    <div class="grid-row-open">
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
	'rowOptions' => function ($model, $key, $index, $grid) {
            $url = Url::to(['lesson/view', 'id' => $model->id]);
        return ['data-url' => $url];
        },
        'tableOptions' =>['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray' ],
        'columns' => $columns,
    ]); ?>
	<?php yii\widgets\Pjax::end(); ?>
    </div>
</div><?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

