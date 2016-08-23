<?php

use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

//$this->title = 'Lessons';
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lesson-index">
<?php if( ! empty($unInvoicedLessonsDataProvider)): ?>
<?php yii\widgets\Pjax::begin(['id' => 'lesson-index']); ?>
    <?php echo GridView::widget([
        'dataProvider' => $unInvoicedLessonsDataProvider,
        'tableOptions' =>['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray' ],
        'columns' => [
			[
				'class' => 'yii\grid\CheckboxColumn',
							// you may configure additional properties here
			],
			[
			    'label' => 'Lesson Id',
			 	'value' => function($data){
					return ! empty($data->id) ? $data->id : null;
				}	
			],
			[
			    'label' => 'Date',
			 	'value' => function($data){
					$date = (new \DateTime($data->date))->format('d-m-Y');
					return ! empty($date) ? $date : null;
				}	
			],
            [
			    'label' => 'Customer Name',
                'value' => function($data) {
                    return ! empty($data->enrolment->student->customer->publicIdentity) ? $data->enrolment->student->customer->publicIdentity : null;
                },
            ],
            [
			    'label' => 'Student Name',
                'value' => function($data) {
                    return ! empty($data->enrolment->student->fullName) ? $data->enrolment->student->fullName : null;
                },
            ],
        ],
    ]); ?>
 <?php yii\widgets\Pjax::end(); ?>
	<?php echo $form->field($model, 'internal_notes')->textarea() ?>
	<?php echo $form->field($model, 'notes')->label('Printed Notes')->textarea() ?>
<?php endif;?>
</div>