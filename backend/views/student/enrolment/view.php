<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use common\models\LocationAvailability;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use common\models\Course;
use common\models\CourseSchedule;
use common\models\discount\EnrolmentDiscount;
?>

<?php yii\widgets\Pjax::begin([
	'id' => 'enrolment-grid',
	'timeout' => 6000,
]) ?>	
<div class="col-md-12">	
<?php
	$toolBoxHtml = $this->render('_button', [
		'model' => $model,
	]);
		LteBox::begin([
			'type' => LteConst::TYPE_DEFAULT,
			'boxTools' => $toolBoxHtml,
			'title' => 'Enrolments',
			'withBorder' => true,
		])
		?>
	<?= $this->render('_list', [
		'enrolmentDataProvider' => $enrolmentDataProvider, 
	]); ?>
   		<?php LteBox::end() ?> 
    </div>
    <?php \yii\widgets\Pjax::end(); ?>
<?php
    $locationId = \common\models\Location::findOne(['slug' => \Yii::$app->language])->id;
    $minLocationAvailability = LocationAvailability::find()
        ->where(['locationId' => $locationId])
        ->orderBy(['fromTime' => SORT_ASC])
        ->one();
    $maxLocationAvailability = LocationAvailability::find()
        ->where(['locationId' => $locationId])
        ->orderBy(['toTime' => SORT_DESC])
        ->one();
    $from_time = (new \DateTime($minLocationAvailability->fromTime))->format('H:i:s');
    $to_time = (new \DateTime($maxLocationAvailability->toTime))->format('H:i:s');
?>
<?php
    Modal::begin([
        'header' => '<h4 class="m-0">Delete Enrolment Preview</h4>',
        'id' => 'enrolment-preview-modal',
    ]);
    Modal::end();
?>
<?php Modal::begin([
    'header' => '<h4 class="m-0">New Enrolment</h4>',
    'id' => 'private-enrol-modal',
]); ?>
<?= $this->render('_form-private', [
		'model' => new Course(), 
		'courseSchedule' => new CourseSchedule(),
		'paymentFrequencyDiscount' => new EnrolmentDiscount(),
		'multipleEnrolmentDiscount' => new EnrolmentDiscount(),
		'student' => $model
	]);?>
<?php Modal::end(); ?>
<?php Modal::begin([
    'header' => $this->render('_group-modal-header'),
    'id' => 'group-enrol-modal',
]); ?>

<div id="group-course-content"></div>

<?php Modal::end(); ?>
