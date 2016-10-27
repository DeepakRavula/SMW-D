<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Enrolment */

$this->title = 'Bulk Reschedule';
?>
<div class="enrolment-update">

    <?php echo $this->render('_form', [
        'model' => $model,
        'lastLessonDate' => $lastLessonDate,
        'teacherDetails' => $teacherDetails,
        'durationMinutes' => $durationMinutes,
    ]) ?>

</div>
