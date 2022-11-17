<?php


/* @var $this yii\web\View */
/* @var $model common\models\Enrolment */

$this->title = 'Enrolment Edit';
?>
<div class="enrolment-update">

    <?php echo $this->render('_form', [
        'model' => $model,
        'courseSchedule' => $courseSchedule,
    ]) ?>

</div>
