<?php

use common\models\Lesson;
?>

<?php $lesson = Lesson::findOne(['lesson.id' => $model->reschedule->lessonId]); 
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
