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
use common\models\Location;
use common\models\log\LogHistory;
use backend\models\search\EnrolmentSearch;
use backend\models\search\UnscheduledLessonSearch;

/**
 * List of models.
 */
class ViewAction extends Action
{
    public function run($id)
    {
        $model = $this->findModel($id);
        if ($model) {
            $locationId = Location::findOne(['slug' => Yii::$app->location])->id;
            $enrolments = Enrolment::find()
                    ->joinWith(['course' => function ($query) {
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
            $enrolmentSearchModel=new EnrolmentSearch();
            $unscheduledLessonSearchModel = new UnscheduledLessonSearch();
            $lessonCount = Lesson::find()
                ->studentEnrolment($locationId, $id)
                ->isConfirmed()
                ->notCanceled()
                ->notCompleted()
			    ->notDeleted()
                ->count();
            return $this->controller->render('view', [
                    'model' => $model,
                    'allEnrolments' => $allEnrolments,
                    'lessonDataProvider' => $this->getLessons($id, $locationId),
                    'enrolmentDataProvider' => $this->getEnrolments($id, $locationId),
                    'unscheduledLessonDataProvider' => $this->getUnscheduledLessons($id, $locationId),
                    'examResultDataProvider' => $this->getExamResults($id),
                    'noteDataProvider' => $this->getNotes($id),
                    'logs' => $this->getLogs($id),
                    'enrolmentSearchModel'=> $enrolmentSearchModel,
                    'unscheduledLessonSearchModel' => $unscheduledLessonSearchModel,
                    'lessonCount' => $lessonCount,
                    'groupLessonDataProvider' => $this->getGroupLessons($id, $locationId),
                    'completedLessonDataProvider' => $this->getCompletedLessons($id, $locationId)
                    ]);
        } else {
            $this->controller->redirect(['index', 'StudentSearch[showAllStudents]' => false]);
        }
    }
    
    public function getLogs($id)
    {
        return new ActiveDataProvider([
                'query' => LogHistory::find()
                ->student($id) ]);
    }

    protected function getUnscheduledLessons($id, $locationId)
    {
        $searchModel = new UnscheduledLessonSearch();
        $searchModel->showAll = false;
        $searchModel->load(Yii::$app->request->get());
        $unscheduledLessons = Lesson::find()
               ->studentEnrolment($locationId, $id)
               ->isConfirmed()
               ->joinWith(['privateLesson'])
               ->andWhere(['NOT', ['private_lesson.lessonId' => null]])
               ->orderBy(['private_lesson.expiryDate' => SORT_ASC])
               ->unscheduled()
               ->notRescheduled()
               ->notDeleted();
                if (!$searchModel->showAll) {
                    $unscheduledLessons->notExpired(); 
                } 
            return new ActiveDataProvider([
                'query' => $unscheduledLessons,
        ]);
    }

    protected function getLessons($id, $locationId)
    {
        $lessons = Lesson::find()
                ->studentEnrolment($locationId, $id)
                ->scheduledOrRescheduled()
                ->isConfirmed()
                ->limit(12)
                ->orderBy(['lesson.date' => SORT_ASC])
                ->notDeleted()
                ->privateLessons()
                ->notCompleted();

        return new ActiveDataProvider([
                'query' => $lessons,
                'pagination' => false
        ]);
    }

    protected function getGroupLessons($id, $locationId)
    {
        $lessons = Lesson::find()
                ->studentEnrolment($locationId, $id)
                ->scheduledOrRescheduled()
                ->isConfirmed()
                ->orderBy(['lesson.date' => SORT_ASC])
                ->notDeleted()
                ->groupLessons()
                ->notCompleted();

        return new ActiveDataProvider([
                'query' => $lessons,
        ]);
    }

    protected function getExamResults($id)
    {
        $examResults = ExamResult::find()
                ->andWhere(['exam_result.isDeleted' => false])
                ->andWhere(['studentId' => $id]);

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
                ->andWhere(['instanceId' => $id, 'instanceType' => Note::INSTANCE_TYPE_STUDENT])
                ->orderBy(['createdOn' => SORT_DESC]);

        return new ActiveDataProvider([
                'query' => $notes,
        ]);
    }

    protected function getEnrolments($id, $locationId)
    {
       $searchModel = new EnrolmentSearch();
       $request = Yii::$app->request;
       $searchModel->studentView = true;
       $searchModel->studentId = $id;
        if ($searchModel->load($request->get())) {
            $enrolmentRequest = $request->get('EnrolmentSearch');
            $searchModel->showAllEnrolments = $enrolmentRequest['showAllEnrolments'];
        }
       $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
       return $dataProvider;
    }
    
    protected function findModel($id)
    {
        $locationId = Location::findOne(['slug' => Yii::$app->location])->id;
        $model = Student::find()
            ->notDeleted()
            ->location($locationId)
            ->andWhere(['student.id' => $id])
            ->one();
        return $model;
    }

    protected function getCompletedLessons($id, $locationId)
    {
        $lessons = Lesson::find()
                ->studentEnrolment($locationId, $id)
                ->notPresent()
                ->completed()
                ->isConfirmed()
                ->orderBy(['lesson.date' => SORT_ASC])
                ->notDeleted();

        return new ActiveDataProvider([
                'query' => $lessons,
        ]);
    }
}
