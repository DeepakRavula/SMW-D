<?php

use yii\grid\GridView;
use yii\bootstrap\ActiveForm;
use common\models\Course;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\GroupCourseSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>

<div class="group-course-index"> 
    
   <?php $form = ActiveForm::begin([
        'method' => 'post',
    ]); ?>
    <?php yii\widgets\Pjax::begin() ?>
    <?php echo GridView::widget([
        'dataProvider' => $groupCourseDataProvider,
        'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'columns' => [
            [
                'class' => 'yii\grid\CheckboxColumn',
                'multiple' => false,
                'name' => 'courseId',
                'checkboxOptions' => ['class' => 'courseId'],
                // you may configure additional properties here
            ],
            [
                'label' => 'Course Name',
                'value' => function ($data) {
                    return !empty($data->program->name) ? $data->program->name : null;
                },
            ],
            [
                'label' => 'Teacher Name',
                'value' => function ($data) {
                    return !empty($data->teacher->publicIdentity) ? $data->teacher->publicIdentity : null;
                },
            ],
            [
                'label' => 'Day',
                'value' => function ($data) {
                    $dayList = Course::getWeekdaysList();
                    $day = $dayList[$data->day];

                    return !empty($day) ? $day : null;
                },
            ],
            [
                'attribute' => 'rate',
                'label' => 'Rate',
                'value' => function ($data) {
                    return !empty($data->program->rate) ? $data->program->rate : null;
                },
            ],
            [
                'label' => 'Duration',
                'value' => function ($data) {
                    $length = \DateTime::createFromFormat('H:i:s', $data->duration);

                    return !empty($data->duration) ? $length->format('H:i') : null;
                },
            ],
            [
                'label' => 'Start Date',
                'value' => function ($data) {
                    return !empty($data->startDate) ? Yii::$app->formatter->asDate($data->startDate) : null;
                },
            ],
            [
                'label' => 'End Date',
                'value' => function ($data) {
                    return !empty($data->endDate) ? Yii::$app->formatter->asDate($data->endDate) : null;
                },
            ],
        ],
    ]); ?>
    <?php \yii\widgets\Pjax::end(); ?>
	<div class="form-group">
		<?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary group-course-save', 'name' => 'signup-button']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<script type="text/javascript">
$(document).ready(function(){
	$('#group-course').on('change', '.courseId', function(){
		var checked = $(this).prop('checked');
		$('#group-course .courseId').prop('checked', false);

		if(checked) {
			$(this).prop('checked', true);
		} else {
			$(this).prop('checked', false);
		}
	});
    $('.group-course-save').on('click', function(event){
        var count = $(".courseId:checked").length;
        if(count==0){
            event.preventDefault();
            $('#notification').html("No group course selected").fadeIn().delay(1000).fadeOut();
        }
    });
});
</script>