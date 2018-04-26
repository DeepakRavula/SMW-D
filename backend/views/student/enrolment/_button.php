<?php

use yii\helpers\Url;
use yii\widgets\ActiveForm;
?>

<?php
$form = ActiveForm::begin([
       'action' => Url::to(['student/view', 'id' => $model->id]),
        'method' => 'post',
    'fieldConfig' => [
        'options' => [
            'tag' => false,
        ],
    ],
    ]);
?>
<?php yii\widgets\Pjax::begin(['options'=>['class' => 'm-r-25']]) ?>

<?= $form->field($enrolmentSearchModel, 'showAllEnrolments')->checkbox(['data-pjax' => true]); ?>

<?php \yii\widgets\Pjax::end(); ?>
<?php ActiveForm::end(); ?>
 <div class="student-enrolment-menu-option">
<i class="fa fa-angle-down fa-lg dropdown-toggle" data-toggle="dropdown"></i>
<ul class="dropdown-menu dropdown-menu-right">
	<li><a id="add-private-enrol" href="#">Add Private...</a></li>
	<li><a id="add-group-enrol" href="<?= Url::to(['course/fetch-group', 'studentId' => $model->id, 'courseName' => null]); ?>">Add Group...</a></li>
</ul>
</div>