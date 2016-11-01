<?php

namespace backend\controllers;

use Yii;
use common\models\Student;
use common\models\Enrolment;
use common\models\Lesson;
use common\models\Program;
use common\models\Course;
use backend\models\search\StudentSearch;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use common\models\Invoice;
use yii\web\Response;
use yii\widgets\ActiveForm;
use common\models\TeacherAvailability;
/**
 * StudentController implements the CRUD actions for Student model.
 */
class StudentController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Student models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new StudentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Displays a single Student model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
		$locationId = Yii::$app->session->get('location_id'); 
		$enrolments = Enrolment::find()
				->joinWith(['course' => function($query) use($locationId){
					$query->where(['locationId' => $locationId]);	
				}])
				->notDeleted()
                ->isConfirmed()        
				->andWhere(['studentId' => $model->id]);

		$enrolmentDataProvider = new ActiveDataProvider([
			'query' => $enrolments,
		]);

		$currentDate = new \DateTime();
		$lessons = Lesson::find()
			->joinWith(['course' => function($query) use($locationId,$model){
				$query->joinWith(['enrolment' => function($query) use($locationId,$model){
					$query->where(['enrolment.studentId' => $model->id]);
				}])
			->where(['course.locationId' => $locationId]);	
			}])
			->where(['not', ['lesson.status' => Lesson::STATUS_DRAFTED]])
			->orderBy(['lesson.date' => SORT_ASC])
			->notDeleted();
				
		$lessonDataProvider = new ActiveDataProvider([
			'query' => $lessons,
		]);	
        
        $unscheduledLessons = Lesson::find()
			->joinWith(['course' => function($query) use($locationId,$model){
				$query->joinWith(['enrolment' => function($query) use($locationId,$model){
					$query->where(['enrolment.studentId' => $model->id]);
				}])
			->where(['course.locationId' => $locationId]);	
			}])
			->joinWith(['lessonReschedule'])
            ->andWhere(['lesson_reschedule.lessonId' => null])
            ->joinWith(['privateLesson'])
            ->andWhere(['NOT', ['private_lesson.lessonId' => null]])
            ->orderBy(['private_lesson.expiryDate' => SORT_DESC])
            ->andWhere(['status' => Lesson::STATUS_CANCELED])
            ->notDeleted();
            
        $unscheduledLessonDataProvider = new ActiveDataProvider([
            'query' => $unscheduledLessons,
        ]);    

		$lessonModel = new Lesson();
		$request = Yii::$app->request;
		$response = Yii::$app->response;
        if($lessonModel->load($request->post())) {
           $studentEnrolment = Enrolment::find()
				   ->joinWith(['course' => function($query) use($lessonModel){
					   $query->where(['course.programId' => $lessonModel->programId]);
				   }])
			  		->where(['studentId' => $model->id])
					->one();
            $lessonModel->courseId = $studentEnrolment->courseId;
            $lessonModel->status = Lesson::STATUS_SCHEDULED;
		    $lessonModel->isDeleted = false;
            $lessonDate = \DateTime::createFromFormat('d-m-Y g:i A', $lessonModel->date);
            $lessonModel->date = $lessonDate->format('Y-m-d H:i:s');            
			$lessonModel->duration = $studentEnrolment->course->duration;
            $lessonModel->save();
			Yii::$app->session->setFlash('alert', [
            	    'options' => ['class' => 'alert-success'],
                	'body' => 'Lesson has been created successfully'
            ]);
            	return $this->redirect(['view', 'id' => $model->id,'#' => 'lesson']);
        } else {
			return $this->render('view', [
				'model' => $model,
				'lessonDataProvider' => $lessonDataProvider,
				'enrolmentDataProvider' => $enrolmentDataProvider,
				'unscheduledLessonDataProvider' => $unscheduledLessonDataProvider,
			]);
		}
    }

	
    /**
     * Creates a new Student model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Student();
		$request = Yii::$app->request;
		$user = $request->post('User');
        if ($model->load(Yii::$app->request->post())) {
			$model->customer_id = $user['id'];
			$model->status = Student::STATUS_ACTIVE;
			$model->save();
			Yii::$app->session->setFlash('alert', [
            	'options' => ['class' => 'alert-success'],
                'body' => 'Student has been created successfully'
            ]);
			$roles = ArrayHelper::getColumn(
            	Yii::$app->authManager->getRolesByUser($model->customer_id),
            'name'
        );
			$role = end($roles);
            return $this->redirect(['view','id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Student model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('alert', [
                'options' => ['class' => 'alert-success'],
                'body' => 'Student profile has been updated successfully'
            ]);
            if ((int) $model->status === (int) Student::STATUS_INACTIVE) {
                $url = ['index', 'StudentSearch[showAllStudents]' => false];
            } else {
                $url = ['view', 'id' => $model->id];
            }
            return $this->redirect($url);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

	public function actionEnrolment($id)
    { 
        $model = $this->findModel($id);
		$session = Yii::$app->session;
		$locationId = $session->get('location_id');
		$request = Yii::$app->request;
		$post = $request->post();
		$courseModel = new Course();
		if ($courseModel->load($post)) {
			$dayList = TeacherAvailability::getWeekdaysList();
			$courseModel->locationId = $locationId;
			$courseModel->studentId = $model->id;
			$courseModel->day = array_search($courseModel->day, $dayList);
			$courseModel->save();

        	return $this->redirect(['lesson/review', 'courseId' => $courseModel->id]);
		}
		if (( ! empty($post['courseId'])) && is_array($post['courseId'])) {
			$enrolmentModel = new Enrolment();
			$enrolmentModel->courseId = $post['courseId'][0];	
			$enrolmentModel->studentId = $model->id;
			$enrolmentModel->isDeleted = 0;
			$enrolmentModel->paymentFrequency = Enrolment::PAYMENT_FREQUENCY_FULL;
			$enrolmentModel->save();
			$courseStartDate = new \DateTime($enrolmentModel->course->startDate);
			$courseStartDate = $courseStartDate->format('d-m-Y');
			$courseEndDate = new \DateTime($enrolmentModel->course->endDate);
			$courseEndDate = $courseEndDate->format('d-m-Y');
            return $this->redirect([
				'/invoice/create',
				'Invoice[customer_id]' => $model->customer->id,
				'Invoice[type]' => Invoice::TYPE_PRO_FORMA_INVOICE,
				'LessonSearch[fromDate]' => $courseStartDate,
				'LessonSearch[toDate]' => $courseEndDate,
				'LessonSearch[courseId]' => $enrolmentModel->courseId
			]);
		}
		
		$groupEnrolments = Enrolment::find()
				->select(['courseId'])
				->joinWith(['course' => function($query) use($locationId){
					$query->groupProgram($locationId);
				}])
				->where(['enrolment.studentId' => $model->id]);
		$groupCourses = Course::find()
				->joinWith(['program' => function($query){
					$query->where(['type' => Program::TYPE_GROUP_PROGRAM]);
				}])
				->where(['NOT IN', 'course.id', $groupEnrolments])
				->andWhere(['locationId' => $locationId]);
		$groupCourseDataProvider = new ActiveDataProvider([
			'query' => $groupCourses,
		]);
		
		return $this->render('_course', [
			'model' => $model,
			'groupCourseDataProvider' => $groupCourseDataProvider,
		]);
    }

    /**
     * Deletes an existing Student model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDeleteEnrolment($enrolmentId, $programType, $studentId)
    {
        $this->findModel($studentId);
			$enrolment = Enrolment::findOne(['id' => $enrolmentId]);
			$enrolment->softDelete();
			
		if((int) $programType === Program::TYPE_PRIVATE_PROGRAM){
			$lessons = Lesson::find()
					->where(['courseId' => $enrolment->courseId])
					->andWhere(['>', 'date', (new \DateTime())->format('Y-m-d H:i:s')])
					->all();
			foreach($lessons as $lesson){
				$lesson->softDelete();
			}
		} 
 		Yii::$app->session->setFlash('alert', [
            'options' => ['class' => 'alert-success'],
            'body' => 'Enrolment has been deleted successfully'
        ]);
            return $this->redirect(['view', 'id' => $studentId,'#' => 'enrolment']);
    }
	
	public function actionDeleteEnrolmentPreview($studentId, $enrolmentId, $programType)
    {
		$model = $this->findModel($studentId);
		$enrolmentModel = Enrolment::findOne(['id' => $enrolmentId]); 
			
        return $this->render('delete-enrolment-preview', [
			'model' => $model,
			'enrolmentId' => $enrolmentId,
			'programType' => $programType,
			'enrolmentModel' => $enrolmentModel,
        ]);
    }

    /**
     * Finds the Student model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Student the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
	protected function findModel($id)
	{
		$session	 = Yii::$app->session;
		$locationId	 = $session->get('location_id');
		$model		 = Student::find()->location($locationId)
				->where(['student.id' => $id])->one();
		if ($model !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}

	public function actionFetchProgramRate($id) {
		$response = Yii::$app->response;
		$response->format = Response::FORMAT_JSON;
		$program = Program::findOne(['id' => $id]);
		return $program->rate;
	}
	}
