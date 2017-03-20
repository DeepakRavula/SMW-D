<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\Program;
use common\models\Lesson;
use yii\helpers\Url;
use yii\bootstrap\Modal;

$this->title = $model->student->fullName.' - '.$model->course->program->name;
$this->params['goback'] = Html::a('<i class="fa fa-angle-left fa-2x"></i>', ['student/view', 'id' => $model->student->id], ['class' => 'go-back text-add-new f-s-14 m-t-0 m-r-10']);
?>
<?= $this->render('_view-enrolment', [
    'model' => $model,
]); ?>

<div class="row-fluid p-10">
    
    <?= Html::a('<i class="fa fa-print"></i> Print', ['course/print', 'id' => $model->course->id], ['class' => 'btn btn-default pull-left', 'target' => '_blank']) ?>  
    <?= Html::a('<i class="fa fa-envelope-o"></i> Email Lessons', '#' , [
		'id' => 'schedule-mail-button',	
		'class' => 'btn btn-default pull-left  m-l-20']) ?>
	<?php if ((int) $model->course->program->type !== (int) Program::TYPE_GROUP_PROGRAM) : ?>
		<?php $this->params['action-button'] = Html::a('<i class="fa fa-pencil"></i> Edit', ['update', 'id' => $model->id], ['class' => ' m-l-20 btn btn-sm btn-primary']) ?>
	<?php endif; ?>
    <div class="clearfix"></div>
    <h4><strong><?= 'Schedule of Lessons' ?> </strong></h4> 
    <?php yii\widgets\Pjax::begin(['id' => 'lesson-index']); ?>
        <?php echo GridView::widget([
                'dataProvider' => $lessonDataProvider,
                'tableOptions' => ['class' => 'table table-bordered'],
                'headerRowOptions' => ['class' => 'bg-light-gray'],
                'summary' => '',
                'columns' => [
                    [
                        'value' => function ($data) {
                            $lessonDate = \DateTime::createFromFormat('Y-m-d H:i:s', $data->date);
                            $date = $lessonDate->format('l, F jS, Y @ g:i a');

                            return !empty($date) ? $date : null;
                        },
                    ],
                ],
        ]); ?>
    <?php yii\widgets\Pjax::end(); ?> 
</div>
<?php
Modal::begin([
    'header' => '<h4 class="m-0">Email Preview</h4>',
    'id'=>'schedule-mail-modal',
]);
 echo $this->render('mail/preview', [
		'model' => $model,
]);
Modal::end();
?>
<script>
 $(document).ready(function() {
	 $(document).on('click', '#schedule-mail-button', function (e) {
		$('#schedule-mail-modal').modal('show');
		return false;
  	});
 });
 </script>