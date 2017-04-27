<?php
use common\models\Lesson;
use yii\data\ActiveDataProvider;
use yii\bootstrap\Modal;
use yii\grid\GridView;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;
?>
<?php
$locationId = Yii::$app->session->get('location_id');
$allLessons = Lesson::find()
	->location($locationId)
	->andWhere(['>', 'DATE(date)', (new \DateTime($model->date))->format('Y-m-d')])
	->andWhere(['courseId' => $model->courseId])
	->scheduled()
    ->notDeleted()
	->all();
foreach($allLessons as $lesson) {
	$lessonDate = (new \DateTime($lesson->date))->format('Y-m-d');
	$lessonStartTime = (new \DateTime($lesson->date))->format('H:i:s');
	$lessonDuration =  \DateTime::createFromFormat('H:i:s', $lesson->duration);
	$lessonDuration->modify('+15 minutes');
	$newDuration = explode(':', ($lessonDuration->format('H:i:s')));
	$date = new \DateTime($lesson->date);
	$date->add(new \DateInterval('PT' . $newDuration[0] . 'H' . $newDuration[1] . 'M'));	
	$date->modify('-1 second');
	$lessonEndTime = $date->format('H:i:s');
	$studentId = $lesson->course->enrolment->student->id;
	$conflictedLessonIds = null;
	$teacherLessons = Lesson::find()
		->teacherLessons($locationId, $lesson->teacherId)
		->andWhere(['NOT', ['lesson.id' => $lesson->id]])
		->overlap($lessonDate, $lessonStartTime, $lessonEndTime)
		->all();
	$studentLessons = Lesson::find()
		->studentLessons($locationId, $studentId)
		->andWhere(['NOT', ['lesson.id' => $lesson->id]])
		->overlap($lessonDate, $lessonStartTime, $lessonEndTime)
		->all();
	if(!empty($teacherLessons) || !empty($studentLessons)) {
		$conflictedLessonIds[] = $lesson->id;	
	}
}
$lessons = Lesson::find()
	->location($locationId)
	->andWhere(['>', 'DATE(date)', (new \DateTime($model->date))->format('Y-m-d')])
	->andWhere(['NOT', ['lesson.id' => $conflictedLessonIds]])
	->andWhere(['courseId' => $model->courseId])
	->scheduled()
	->notDeleted();
$lessonDataProvider = new ActiveDataProvider([
	'query' => $lessons,
	'pagination' => false
]);
?>
<div>
 <?php
 Modal::begin([
    'header' => '<h4 class="m-0">Split Lesson</h4>',
    'id'=>'split-lesson-modal',
]);?>
	<h5><strong><?= 'Please choose the lessons that should be extended when splitting this lesson.'; ?></strong></h5>
	<?php $form = ActiveForm::begin([
		'id' => 'split-lesson-form',
		'action' => Url::to(['lesson/split', 'id' => $model->id]),
	]); ?>
	<div>
	<?php
    echo GridView::widget([
        'dataProvider' => $lessonDataProvider,
        'options' => ['class' => 'col-md-12'],
        'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'columns' => [
			[
                'class' => 'yii\grid\CheckboxColumn',
				'name' => 'splitLessonIds'
            ],
            [
                'label' => 'Program',
                'value' => function ($data) {
                    return !empty($data->course->program->name) ? $data->course->program->name : null;
                },
            ],
            [
                'label' => 'Status',
                'value' => function ($data) {
                    $lessonDate = \DateTime::createFromFormat('Y-m-d H:i:s', $data->date);
                    $currentDate = new \DateTime();

                    if ($lessonDate <= $currentDate) {
                        $status = 'Completed';
                    } else {
                        $status = 'Scheduled';
                    }

                    return $status;
                },
            ],
            [
                'label' => 'Invoice Status',
                'value' => function ($data) {
                    $status = null;
                    if (!empty($data->invoice)) {
                        return $data->invoice->getStatus();
                    } else {
                        $status = 'Not Invoiced';
                    }

                    return $status;
                },
            ],
            [
                'label' => 'Date',
                'value' => function ($data) {
                    return Yii::$app->formatter->asDate($data->date).' @ '.Yii::$app->formatter->asTime($data->date);
                },
            ],
            [
                'label' => 'Prepaid?',
                'value' => function ($data) {
                    if (!empty($data->proFormaInvoice) && ($data->proFormaInvoice->isPaid() || $data->proFormaInvoice->hasCredit())) {
                        return 'Yes';
                    }

                    return 'No';
                },
            ],
        ],
    ]);
	?>
	</div>
	<?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
	<?php ActiveForm::end(); ?>
<?php Modal::end(); ?>
</div>
