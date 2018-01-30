<?php

use yii\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\data\ActiveDataProvider;
use common\models\Lesson;
use backend\models\EmailForm;

?>

<div class="row-fluid p-10">
    <div class="pull-left">
	<p>
    <h4><strong class="m-r-10"><?= 'Schedule of Lessons' ?></strong> 
        <?= Html::a('<i class="fa fa-print"></i> ', ['print/course', 'id' => $model->course->id], ['class' => 'm-r-10', 'target' => '_blank']) ?>  
        <?= Html::a('<i class="fa fa-envelope-o"></i> ', '#', [
        'id' => 'schedule-mail-button',
        'class' => '']) ?> </h4></p>
    </div>
    <div class="clearfix"></div>
    <?php Pjax::begin(['id' => 'lesson-index']); ?>
        <?php echo GridView::widget([
                'dataProvider' => $lessonDataProvider,
                'summary' => false,
                'emptyText' => false,
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
    $lessonDataProvider = new ActiveDataProvider([
        'query' => Lesson::find()
            ->andWhere([
                'courseId' => $model->course->id,
                'status' => Lesson::STATUS_SCHEDULED
            ])
            ->isConfirmed()
            ->notDeleted()
            ->orderBy(['lesson.date' => SORT_ASC]),
            'pagination' => [
                'pageSize' => 60,
             ],
    ]);
    $body = null;
    $body = 'Please find the lesson schedule for the program you enrolled on ' . Yii::$app->formatter->asDate($model->course->startDate) ;
    $content = $this->render('mail/content', [
        'toName' => $model->student->customer->publicIdentity,
        'content' => $body,
        'model' => $model,
        'lessonDataProvider' => $lessonDataProvider
    ]);
    $emails = !empty($model->student->customer->email) ? $model->student->customer->email : null;
?>
<?php
Modal::begin([
    'header' => '<h4 class="m-0">Email Preview</h4>',
    'id'=>'schedule-mail-modal',
]);
echo $this->render('/mail/_form', [
    'model' => new EmailForm(),
    'emails' => $emails,
    'subject' => 'Schedule for ' . $model->student->fullName,
    'content' => $content,
    'id' => null,
        'userModel'=>$model->student->customer,
]);
Modal::end();
?>
<script>
 $(document).ready(function() {
	 $(document).on('click', '#schedule-mail-button', function (e) {
		$('#schedule-mail-modal').modal('show');
		return false;
  	});
    $(document).on("click", '.mail-view-cancel-button', function() {
		$('#schedule-mail-modal').modal('hide');
		return false;
    });
	$(document).on('beforeSubmit', '#mail-form', function (e) {
		$.ajax({
			url    : $(this).attr('action'),
			type   : 'post',
			dataType: "json",
			data   : $(this).serialize(),
			success: function(response)
			{
			   if(response.status)
			   	{
                    $('#spinner').hide();		
                    $('#schedule-mail-modal').modal('hide');
					$('#success-notification').html(response.message).fadeIn().delay(5000).fadeOut();
				}
			}
		});
		return false;
	});
 });
 </script>

