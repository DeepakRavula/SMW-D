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
<?php if( ! empty($unInvoicedLessonsDataProvider)): ?>
<?php yii\widgets\Pjax::begin(['id' => 'lesson-index']); ?>
    <?php echo GridView::widget([
        'dataProvider' => $unInvoicedLessonsDataProvider,
        'columns' => [
			[
				'class' => 'yii\grid\CheckboxColumn',
							// you may configure additional properties here
			],
            ['class' => 'yii\grid\SerialColumn'],

   			'id',
            [
			    'label' => 'Customer Name',
                'value' => function($data) {
                    return ! empty($data->enrolmentScheduleDay->enrolment->student->customer->publicIdentity) ? $data->enrolmentScheduleDay->enrolment->student->customer->publicIdentity : null;
                },
            ],
            [
			    'label' => 'Student Name',
                'value' => function($data) {
                    return ! empty($data->enrolmentScheduleDay->enrolment->student->fullName) ? $data->enrolmentScheduleDay->enrolment->student->fullName : null;
                },
            ],
        ],
    ]); ?>
 <?php yii\widgets\Pjax::end(); ?>
<?php endif;?>
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
