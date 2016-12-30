<?php

use yii\helpers\Html;
use yii\bootstrap\Tabs;
use yii\helpers\Url;
use common\models\Note;
use common\models\Lesson;

/* @var $this yii\web\View */
/* @var $model common\models\Student */
$this->title = 'Lesson Details';
$this->params['goback'] = Html::a('<i class="fa fa-angle-left fa-2x"></i>', ['index', 'LessonSearch[type]' => Lesson::TYPE_PRIVATE_LESSON], ['class' => 'go-back text-add-new f-s-14 m-t-0 m-r-10']);
?>
 <div class="tabbable-panel">
     <div class="tabbable-line">
<?php 

$lessonContent = $this->render('_view', [
    'model' => $model,
]);

$noteContent = $this->render('note/view', [
	'model' => new Note(),
	'noteDataProvider' => $noteDataProvider
]);

?>
<?php echo Tabs::widget([
    'items' => [
        [
            'label' => 'Lesson',
            'content' => $lessonContent,
            'options' => [
                    'id' => 'lesson',
                ],
        ],
		[
            'label' => 'Notes',
            'content' => $noteContent,
            'options' => [
                'id' => 'note',
            ],
        ],
    ],
]);
?>
<div class="clearfix"></div>
     </div>
 </div>
<script>
 $(document).ready(function() {
	$(document).on('click', '#lesson-note', function (e) {
		$('#lesson-note-modal').modal('show');
		return false;
  	});
	$(document).on('beforeSubmit', '#lesson-note-form', function (e) {
		$.ajax({
			url    : '<?= Url::to(['lesson/add-note', 'id' => $model->id]); ?>',
			type   : 'post',
			dataType: "json",
			data   : $(this).serialize(),
			success: function(response)
			{
			   if(response.status)
			   {
					$.pjax.reload({container : '#lesson-note-listing', timeout : 12000});
					$('#lesson-note-modal').modal('hide');
				}else
				{
				 $('#lesson-note-form').yiiActiveForm('updateMessages',
					   response.errors
					, true);
				}
			}
		});
		return false;
	});
});
</script>


