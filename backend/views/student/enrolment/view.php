<?php

use yii\bootstrap\Modal;
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
