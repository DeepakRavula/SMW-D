<?php

use common\models\Lesson;

?>

<?php $originalLesson=Lesson::findOne(['lesson.id' => $model->id]);?>
<table>
        <td>Teacher</td>
        <td><?=$originalLesson->teacher->publicIdentity;?></td>
    </tr>
    <tr>
        <td>Program</td>
        <td><?=$originalLesson->course->program->name;?></td>
    </tr>
    <tr>
        <td>Day & Date</td>
        <td><?=Yii::$app->formatter->asDate($originalLesson->date);?></td>
    </tr>
    <tr>
        <td>Time</td>
        <td><?=Yii::$app->formatter->asTime($originalLesson->date);?></td>
    </tr>
    <tr>
       <?php  $duration = \DateTime::createFromFormat('H:i:s', $originalLesson->duration);
$lessonDuration = ($duration->format('H') * 60) + $duration->format('i');?>
        <td>Duration</td>
        <td><?=$lessonDuration.'   minutes';?></td>
    </tr>
    <?php if($originalLesson->isPrivate()):?>
        <tr>
        <td>Student Name</td>
        <td><?=$originalLesson->enrolment->student->fullname;?></td>
    </tr>
    <tr>
        <td>Customer</td>
        <td><?=$originalLesson->enrolment->student->customer->publicIdentity;?></td>
    </tr>
    <tr>
   <tr>
        <td>Expiry Date</td>
        <td><?=Yii::$app->formatter->asDate($originalLesson->privateLesson->expiryDate);?></td>
    </tr>
    <?php endif; ?>
</table>
<?php if (!empty($model->reschedule) && !empty($model->enrolment)) : ?>
<?php 
$lesson = Lesson::findOne(['lesson.id' => $model->reschedule->lessonId]);
$duration = \DateTime::createFromFormat('H:i:s', $model->duration);
$lessonDuration = ($duration->format('H') * 60) + $duration->format('i');
$duration = \DateTime::createFromFormat('H:i:s', $lesson->duration);
$oldLessonDuration = ($duration->format('H') * 60) + $duration->format('i');
$studentName = $lesson->enrolment->student->fullname;
$programName = $lesson->course->program->name;
$teacherName = $lesson->teacher->publicIdentity;
$oldLessonDate = (new \DateTime($lesson->date))->format('l, F jS, Y');
$oldLessonTime = Yii::$app->formatter->asTime($lesson->date);
$lessonTime = Yii::$app->formatter->asTime($model->date);
$lessonDate = (new \DateTime($model->date))->format('l, F jS, Y');
$newTeacherName = $model->teacher->publicIdentity;
?>
<?php if (!empty($model->date) && new \DateTime($model->date) != new \DateTime($lesson->date)) : ?>
    <?= $studentName . '\'s ' . $programName . ' lesson with ' . $teacherName . ' on ' . $oldLessonDate . ' @ ' . $oldLessonTime . ' for ' . $oldLessonDuration . ' minutes has been rescheduled to ' . $lessonDate . ' @ ' . $lessonTime . ' for ' . $lessonDuration . ' minutes.'; ?>
<?php elseif ((int)$model->teacherId !== (int)$lesson->teacherId) : ?>
    <?= $studentName . '\'s ' . $programName . ' lesson with ' . $teacherName . ' on ' . $oldLessonDate . ' @ ' . $oldLessonTime . ' for ' . $oldLessonDuration . ' minutes has been rescheduled to ' . $lessonDate . ' @ ' . $lessonTime . ' for ' . $lessonDuration . ' minutes ' . $newTeacherName; ?>
<?php endif; ?>
<?php endif; ?>