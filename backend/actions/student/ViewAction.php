<?php
namespace backend\actions\student;

use yii\base\Action;
use Yii;
use common\models\Student;
use common\models\Enrolment;
use yii\data\ActiveDataProvider;
use common\models\Lesson;
use common\models\ExamResult;
use common\models\Note;


/**
 * List of models.
 */
class ViewAction extends Action
{
    public function run($id)
    {
       	if(!empty($this->findModel($id))) {
        	$model = $this->findModel($id);
			$locationId = Yii::$app->session->get('location_id');
			$query = Enrolment::find()
				->joinWith(['course' => function($query) {
					$query->isConfirmed();
				}])
				->location($locationId)
				->notDeleted()
				->isConfirmed()
				->andWhere(['studentId' => $model->id]);
			$enrolments = $query->all();
			$allEnrolments = [];
			foreach ($enrolments as $enrolment) {
				$allEnrolments[] = [
					'teacherId' => $enrolment->course->teacherId,
					'programId' => $enrolment->course->programId
				];
			}
			$enrolmentDataProvider = new ActiveDataProvider([
				'query' => $query->isRegular(),
			]);

			$currentDate = new \DateTime();
			$lessons = Lesson::find()
				->studentEnrolment($locationId, $model->id)
				->where(['lesson.status' => [Lesson::STATUS_SCHEDULED, Lesson::STATUS_COMPLETED, Lesson::STATUS_MISSED]])
				->isConfirmed()
				->orderBy(['lesson.date' => SORT_ASC])
				->notDeleted();

			$lessonDataProvider = new ActiveDataProvider([
				'query' => $lessons,
			]);

			$unscheduledLessons = Lesson::find()
				->studentEnrolment($locationId, $model->id)
				->isConfirmed()
				->joinWith(['privateLesson'])
				->andWhere(['NOT', ['private_lesson.lessonId' => null]])
				->orderBy(['private_lesson.expiryDate' => SORT_DESC])
				->unscheduled()
				->notRescheduled()
				->notDeleted();

			$unscheduledLessonDataProvider = new ActiveDataProvider([
				'query' => $unscheduledLessons,
			]);    

			$examResults = ExamResult::find()
				->where(['studentId' => $model->id]);

			$examResultDataProvider = new ActiveDataProvider([
				'query' => $examResults,
				'pagination' => [
					'pageSize' => 5,
				]
			]);

			$notes = Note::find()
				->where(['instanceId' => $model->id, 'instanceType' => Note::INSTANCE_TYPE_STUDENT])
				->orderBy(['createdOn' => SORT_DESC]);

			$noteDataProvider = new ActiveDataProvider([
				'query' => $notes,
			]);

			return $this->controller->render('view', [
				'model' => $model,
				'allEnrolments' => $allEnrolments,
				'lessonDataProvider' => $lessonDataProvider,
				'enrolmentDataProvider' => $enrolmentDataProvider,
				'unscheduledLessonDataProvider' => $unscheduledLessonDataProvider,
				'examResultDataProvider' => $examResultDataProvider,
				'noteDataProvider' => $noteDataProvider
				]);
		} else {
			$this->controller->redirect(['index', 'StudentSearch[showAllStudents]' => false]);
		} 
	}
	protected function findModel($id)
    {
        $session = Yii::$app->session;
        $locationId = $session->get('location_id');
        $model = Student::find()
			->notDeleted()
			->location($locationId)
			->where(['student.id' => $id])->one();
        return $model;
    }
}