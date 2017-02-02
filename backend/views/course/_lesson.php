<?php

use yii\grid\GridView;
use yii\helpers\Url;
use common\models\Course;
use yii\bootstrap\ActiveForm;
use common\models\Lesson;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lessons';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-search">

    <?php $form = ActiveForm::begin(); ?>
    <div class="col-md-12">
        <div class="e1Div schedule-index">
        <?= $form->field($model, 'lessonStatus')->checkbox(['data-pjax' => true])->label('Show All'); ?>
		</div>
	</div>
	<div class="clearfix"></div>
    <?php ActiveForm::end(); ?>

</div>
<div class="group-course-lessons">
	<?= $this->render('_lesson-list', [
    	'lessonDataProvider' => $lessonDataProvider,
    	'model' => $model,
	]);?>
</div>
<script>
 $(document).ready(function() {
	$(document).on('change', '#course-lessonstatus', function (e) {
      var lessonStatus = $(this).is(":checked");
	  console.log(lessonStatus);
		$.ajax({
			url    : '<?= Url::to(['course/fetch-lessons', 'id' => $model->id]);?>&lessonStatus=' + (lessonStatus | 0),
			type   : 'get',
			dataType: "json",
			success: function(response)
			{
			   if(response)
			   {
					$('.group-course-lessons').html(response);
				}
			}
		});
		return false;
	});
});
</script>