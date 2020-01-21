<?php

use common\models\Lesson;

?>

<table>
   <?php if($model->isPrivate()):?>
        <tr>
        <td>Student</td>
        <td><?=$model->enrolment->student->fullname;?></td>
    </tr>
    <tr>
        <td>Customer</td>
        <td><?=$model->enrolment->student->customer->publicIdentity;?></td>
    </tr>
    <tr>
        <?php endif;?>
    <?php if ($model->hasSubstituteByTeacher()) : ?>
        <?php $teacher = $model->getOriginalTeacher()  ; ?>
    <tr>
        <td>Original Teacher</td>
        <td><?= $teacher ?></td>
    </tr>
    <?php endif; ?>
    <tr>
        <td>Teacher</td>
        <td><?= $model->teacher->publicIdentity ?>
        </td>
    </tr>
    <?php if ($model->isRescheduled() || $model->isUnscheduled()) : ?>
    <tr>
        <td>Original Date</td>
        <td><?= (new \DateTime($model->getOriginalDate()))->format('l, F jS, Y'); ?></td>
    </tr>
    <?php endif; ?>
    <?php if (!($model->isUnscheduled())) : ?>
    <tr>
        <td>Scheduled Date</td>
        <td><?= (new \DateTime($model->date))->format('l, F jS, Y'); ?></td>
    </tr>
    <?php endif; ?>
    <tr>
    <td>Time</td>
    <td><?= Yii::$app->formatter->asTime($model->date); ?></td>
    </tr>
    <tr>
    <td>Duration</td>
    <td><?= (new \DateTime($model->duration))->format('H:i'); ?></td>
    </tr>
    <tr>
    <td>Status</td>
	<td><?= $model->getStatus(); ?></td>
    </tr>
<?php if ($model->privateLesson) : ?>
    <tr>
    <td>Expiry Date</td>
    <td><?= Yii::$app->formatter->asDate($model->privateLesson->expiryDate); ?></td>
    </tr>
<?php endif; ?>

</table>
<?php if (!empty($model->reschedule) && !empty($model->enrolment) && !$model->bulkRescheduleLesson) : ?>
<?php 
$lesson = Lesson::findOne(['lesson.id' => $model->reschedule->lessonId]);
$duration = \DateTime::createFromFormat('H:i:s', $model->duration);
$lessonDuration = ($duration->format('H') * 60) + $duration->format('i');
$duration = \DateTime::createFromFormat('H:i:s', $lesson->duration);
$oldLessonDuration = ($duration->format('H') * 60) + $duration->format('i');
$studentName = $lesson->enrolment->student->fullname;
$programName = $lesson->course->program->name;
$teacherName = $lesson->teacher->publicIdentity;
$oldLessonDate = (new \DateTime($model->originalDate))->format('l, F jS, Y');
$oldLessonTime = Yii::$app->formatter->asTime($model->getOriginalDate());
$lessonTime = Yii::$app->formatter->asTime($model->date);
$lessonDate = (new \DateTime($model->date))->format('l, F jS, Y');
$newTeacherName = $model->teacher->publicIdentity;
?>
<?php if (!empty($model->date) && new \DateTime($model->date) != new \DateTime($lesson->date)) : ?>
    <?= $studentName . '\'s ' . $programName . ' lesson with ' . $teacherName . ' on ' . $oldLessonDate . ' @ ' . $oldLessonTime . ' for ' . $oldLessonDuration . ' minutes has been rescheduled to ' . $lessonDate . ' @ ' . $lessonTime . ' for ' . $lessonDuration . ' minutes with ' . $newTeacherName. ".";?>
<?php elseif ((int)$model->teacherId !== (int)$lesson->teacherId) : ?>
    <?= $studentName . '\'s ' . $programName . ' lesson with ' . $teacherName . ' on ' . $oldLessonDate . ' @ ' . $oldLessonTime . ' for ' . $oldLessonDuration . ' minutes has been rescheduled to ' . $lessonDate . ' @ ' . $lessonTime . ' for ' . $lessonDuration . ' minutes with  ' . $newTeacherName. "."; ?>
<?php endif; ?>
<?php endif; ?>