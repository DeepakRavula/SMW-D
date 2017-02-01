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
        <?= $form->field($model, 'lessonStatus')->radioList(Course::lessonStatuses())->label(false) ?>
	</div>
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
		var lessonStatus = $("input[type='radio'][name='Course[lessonStatus]']:checked").val();
		$.ajax({
			url    : '<?= Url::to(['course/fetch-lessons', 'id' => $model->id]);?>&lessonStatus=' + lessonStatus,
			type   : 'get',
			dataType: "json",
			data   : $(this).serialize(),
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