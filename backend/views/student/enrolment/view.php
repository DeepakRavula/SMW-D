<?php

use yii\bootstrap\Tabs;
use common\models\Course;
use yii\helpers\Url;
use common\models\User;

/* @var $this yii\web\View */
/* @var $model common\models\Student */
$this->title = 'Student Enrolment';

?>
<div id="enrolment-discount-warning" style="display:none;" class="alert-warning alert fade in"></div>
<div class="user-details-wrapper">
	<div class="col-md-12  p-l-0">
		<p class="users-name"><?php echo $model->fullName; ?> </p>
	</div>
	<div class="col-md-2 hand red-text p-l-0" data-toggle="tooltip" data-placement="bottom" title="Birth date">
		<i class="fa fa-birthday-cake detail-icon"></i> <?php echo !empty($model->birth_date) ? Yii::$app->formatter->asDate($model->birth_date) : null; ?>
	</div>
	<div class="col-md-3 hand p-l-0" data-toggle="tooltip" data-placement="bottom" title="Customer">
		<a href="<?= Url::to(['/user/view','UserSearch[role_name]' => User::ROLE_CUSTOMER,'id' => $model->customer->id]); ?>">
		<i class="fa fa-user detail-icon"></i> <?php echo !empty($model->customer->userProfile->fullName) ? $model->customer->userProfile->fullName : null ?>
	</a>
	</div>
	<div class="clearfix"></div>
</div>
 <div class="nav-tabs-custom">
<?php 

$privateCourseContent = $this->render('_form-private', [
    'model' => new Course(),
]);

$groupCourseContent = $this->render('_form-group', [
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