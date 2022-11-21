<?php

use common\models\Program;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use common\models\User;
use common\models\Enrolment;

?>
<?php
LteBox::begin([
    'type' => LteConst::TYPE_DEFAULT,
    'title' => 'Details',
    'withBorder' => true,
])
?>
<dl class="dl-horizontal">
	<?php if ($model->isBulkTeacherChange) : ?>
		<?php $oldTeacher = User::findOne($model->teacherId);
			$newTeacher = User::findOne($newTeacherId); 
			$enrolments = Enrolment::find()
					->notDeleted()
					->isConfirmed()
					->andWhere(['id' => $model->enrolmentIds])
					->all();
			$students = [];
			foreach ($enrolments as $enrolment) {
				if ($enrolment->student) {
					$students[] = $enrolment->student->fullName;
				}
			}
			$listOfStudents = implode(" , ", $students);
			?>
			<dt>Old Teacher</dt>
			<dd><?= $oldTeacher->publicIdentity; ?></dd>
			<dt>New Teacher</dt>
			<dd><?= $newTeacher->publicIdentity; ?></dd>
			<dt>Students</dt>
			<dd><?= $listOfStudents; ?></dd>
			<dt>As of</dt>
			<dd><?= $model->changesFrom; ?></dd>
	<?php else : ?>
		<?php if ($courseModel) : ?>
			<?php if ((int) $courseModel->program->type === Program::TYPE_PRIVATE_PROGRAM) :?>
				<dt>Student</dt>
				<dd><?= $courseModel->enrolment->student->fullName; ?></dd>
			<?php endif; ?>
			<dt>Program</dt>
			<dd><?= $courseModel->program->name; ?></dd>
			<dt>Teacher</dt>
			<dd><?= $courseModel->teacher->publicIdentity; ?></dd>
			<dt>Period</dt>
			<dd><?= Yii::$app->formatter->asDate($courseModel->startDate) . ' to ' . Yii::$app->formatter->asDate($courseModel->endDate)?></dd>
			<dt>Time</dt>
			<dd><?= (new \DateTime($courseModel->recentCourseSchedule->fromTime))->format('h:i A');?></dd>
		<?php elseif ($model->enrolmentIds) : ?>
		<?php $enrolment = Enrolment::findOne(end($model->enrolmentIds));
			$teacher = User::findOne($model->teacherId); 
		?>
			<dt>Program</dt>
			<dd><?= $enrolment->program->name; ?></dd>
			<dt>Teacher</dt>
			<dd><?= $teacher->publicIdentity; ?></dd>
		<?php endif; ?>
	<?php endif; ?>
</dl>
<?php LteBox::end()?>