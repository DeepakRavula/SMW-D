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
			$enrolments = Enrolment::find()
				->joinWith(['course' => function($query) {
					$query->isConfirmed();
				}])
				->location($locationId)
				->notDeleted()
				->isConfirmed()
				->andWhere(['studentId' => $model->id])
				->all();
			$allEnrolments = [];
			foreach ($enrolments as $enrolment) {
				$allEnrolments[] = [
					'teacherId' => $enrolment->course->teacherId,
					'programId' => $enrolment->course->programId
				];
			}
			return $this->controller->render('view', [
				'model' => $model,
				'allEnrolments' => $allEnrolments,
				'lessonDataProvider' => $this->getLessons($id, $locationId),
				'enrolmentDataProvider' => $this->getEnrolments($id, $locationId),
				'unscheduledLessonDataProvider' => $this->getUnscheduledLessons($id, $locationId),
				'examResultDataProvider' => $this->getExamResults($id),
				'noteDataProvider' => $this->getNotes($id),
				]);
		} else {
			$this->controller->redirect(['index', 'StudentSearch[showAllStudents]' => false]);
		} 
	}
	protected function getUnscheduledLessons($id, $locationId)
	{
		$unscheduledLessons = Lesson::find()
			->studentEnrolment($locationId, $id)
			->isConfirmed()
			->joinWith(['privateLesson'])
			->andWhere(['NOT', ['private_lesson.lessonId' => null]])
			->orderBy(['private_lesson.expiryDate' => SORT_DESC])
			->unscheduled()
			->notRescheduled()
			->notDeleted();

		return new ActiveDataProvider([
			'query' => $unscheduledLessons,
		]);  
	}

	protected function getLessons($id, $locationId)
	{
		$lessons = Lesson::find()
			->studentEnrolment($locationId, $id)
			->where(['lesson.status' => [Lesson::STATUS_SCHEDULED, Lesson::STATUS_COMPLETED, Lesson::STATUS_MISSED]])
			->isConfirmed()
			->orderBy(['lesson.date' => SORT_ASC])
			->notDeleted();

		return new ActiveDataProvider([
			'query' => $lessons,
		]);
	}

	protected function getExamResults($id)
	{
		$examResults = ExamResult::find()
			->where(['studentId' => $id]);

		return new ActiveDataProvider([
			'query' => $examResults,
			'pagination' => [
				'pageSize' => 5,
			]
		]);
	}

	protected function getNotes($id)
	{
		$notes = Note::find()
			->where(['instanceId' => $id, 'instanceType' => Note::INSTANCE_TYPE_STUDENT])
			->orderBy(['createdOn' => SORT_DESC]);

		return new ActiveDataProvider([
			'query' => $notes,
		]);
	}
	
	protected function getEnrolments($id, $locationId)
	{
		$query = Enrolment::find()
			->joinWith(['course' => function($query) {
				$query->isConfirmed();
			}])
			->location($locationId)
			->notDeleted()
			->isConfirmed()
			->andWhere(['studentId' => $id])
			->isRegular();
		
		return new ActiveDataProvider([
			'query' => $query,
		]);
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