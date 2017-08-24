<?php

use yii\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\widgets\Pjax;
?>

<div class="row-fluid p-10">
    <div class="pull-left">
	<p>
    <h4><strong class="m-r-10"><?= 'Schedule of Lessons' ?></strong> 
        <?= Html::a('<i class="fa fa-print"></i> ', ['print/course', 'id' => $model->course->id], ['class' => 'm-r-10', 'target' => '_blank']) ?>  
        <?= Html::a('<i class="fa fa-envelope-o"></i> ', '#' , [
        'id' => 'schedule-mail-button', 
        'class' => '']) ?> </h4></p>
    </div>
    <div class="clearfix"></div>
    <?php Pjax::begin(['id' => 'lesson-index']); ?>
        <?php echo GridView::widget([
                'dataProvider' => $lessonDataProvider,
                'tableOptions' => ['class' => 'table table-bordered'],
				'showHeader' => false,
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
    <?php Pjax::end(); ?> 
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

