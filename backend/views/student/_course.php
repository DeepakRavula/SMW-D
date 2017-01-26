<?php

use yii\bootstrap\Tabs;
use common\models\Course;

/* @var $this yii\web\View */
/* @var $model common\models\Student */
$this->title = 'Student Enrolment';

?>
<style>
    .wizard li.active span.round-tab{
        border: 2px solid #ea212d;
    }
    .wizard li.active span.round-tab i{
        color:#ea212d;
    }
    .wizard li.active:after{
        left:50%;
        border-bottom-color:#ea212d;
    }
    .wizard li span.round-tab{
        font-size: 16px;
    }
</style>
<div class="user-details-wrapper">
	<div class="col-md-12  p-l-0">
		<p class="users-name"><?php echo $model->fullName; ?> </p>
	</div>
	<div class="col-md-2 hand red-text p-l-0" data-toggle="tooltip" data-placement="bottom" title="Birth date">
		<i class="fa fa-birthday-cake detail-icon"></i> <?php echo !empty($model->birth_date) ? Yii::$app->formatter->asDate($model->birth_date) : null; ?>
	</div>
	<div class="col-md-3 hand p-l-0" data-toggle="tooltip" data-placement="bottom" title="Customer">
		<a href="/user/view?UserSearch%5Brole_name%5D=customer&id=<?php echo $model->customer->id ?>">
		<i class="fa fa-user detail-icon"></i> <?php echo !empty($model->customer->userProfile->fullName) ? $model->customer->userProfile->fullName : null ?>
	</a>
	</div>
	<div class="clearfix"></div>
</div>
 <div class="tabbable-panel">
     <div class="tabbable-line">
<?php 

$privateCourseContent = $this->render('_form-private-course', [
    'model' => new Course(),
]);

$groupCourseContent = $this->render('_form-group-course', [
    'groupCourseDataProvider' => $groupCourseDataProvider,
]);

?>
<?php echo Tabs::widget([
    'items' => [
        [
            'label' => 'Private Course',
            'content' => $privateCourseContent,
            'options' => [
                    'id' => 'private-course',
                ],
        ],
        [
            'label' => 'Group Course',
            'content' => $groupCourseContent,
            'options' => [
                    'id' => 'group-course',
                ],
        ],
    ],
]);
?>
<div class="clearfix"></div>
</div>
 </div>