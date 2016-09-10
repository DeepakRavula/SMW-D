<?php

namespace backend\controllers;

use Yii;
use common\models\Student;
use common\models\Enrolment;
use common\models\Lesson;
use common\models\Program;
use common\models\GroupCourse;
use common\models\GroupEnrolment;
use backend\models\search\StudentSearch;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

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
				->where(['studentId' => $model->id])
				->all();

		$currentDate = new \DateTime();
		$query = Lesson::find()
				->joinWith(['course' => function($query) use($location_id,$model){
					$query->where(['course.locationId' => $locationId,'enrolment.studentId' => $model->id]);
				}])
				->where(['not', ['lesson.status' => Lesson::STATUS_DRAFTED]]);
				
		$lessonDataProvider = new ActiveDataProvider([
			'query' => $query,
		]);	

		$enrolmentModel = new Enrolment();
        $lessonModel = new Lesson();
        if($lessonModel->load(Yii::$app->request->post()) ){
           $studentEnrolmentModel = Enrolment::findOne([
			   'studentId' => $model->id,
			   'program_id' => $lessonModel->program_id
		   ]);
           $lessonModel->enrolmentId = $studentEnrolmentModel->id; 
           $lessonModel->status = Lesson::STATUS_DRAFTED;
		   $lessonModel->isDeleted = 0;
           $lessonDate = \DateTime::createFromFormat('d-m-Y g:i A', $lessonModel->date);
           $lessonModel->date = $lessonDate->format('Y-m-d H:i:s');            
           $lessonModel->save();
           Yii::$app->session->setFlash('alert', [
            	    'options' => ['class' => 'alert-success'],
                	'body' => 'Lesson has been added successfully'
            ]);
            	return $this->redirect(['view', 'id' => $model->id,'#' => 'lesson']);
        }
        if ($enrolmentModel->load(Yii::$app->request->post()) ) {
			$enrolmentModel->student_id = $id;
			$enrolmentModel->isDeleted = 0;
			$enrolmentModel->save();
			    Yii::$app->session->setFlash('alert', [
            	    'options' => ['class' => 'alert-success'],
                	'body' => 'Student has been enrolled successfully'
            ]);
            	return $this->redirect(['lesson-review', 'id' => $model->id,'enrolmentId' => $enrolmentModel->id]);
        } else {
            return $this->render('view', [
            	'model' => $model,
                'lessonDataProvider' => $lessonDataProvider,
                'enrolmentModel' => $enrolmentModel,
                'lessonModel' => $lessonModel,
				'privateLessons' => $privateLessons,
				'groupCourses' => $groupCourses
            ]);
        }
    }

	public function actionLessonReview($id, $enrolmentId){
		$model = $this->findModel($id);
		$lessonDataProvider = new ActiveDataProvider([
			'query' => Lesson::find()
				->where(['enrolment_id' => $enrolmentId, 'status' => Lesson::STATUS_DRAFTED]),
		]);
		
		return $this->render('lesson-review', [
            	'model' => $model,
				'enrolmentId' => $enrolmentId,
                'lessonDataProvider' => $lessonDataProvider,
            ]);	
	}

	public function actionLessonConfirm($id, $enrolmentId){
		$model = $this->findModel($id);
		Lesson::updateAll(['status' => Lesson::STATUS_SCHEDULED], ['enrolment_id' => $enrolmentId]);
		
		Yii::$app->session->setFlash('alert', [
				'options' => ['class' => 'alert-success'],
				'body' => 'Lessons have been created successfully'
		]);
        return $this->redirect(['view', 'id' => $model->id,'#' => 'lesson']);
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
			$model->isDeleted = 0;
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
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Enrols a student to the chosen program
     * If update is successful, the browser will be redirected to the student's 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionEnrol($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
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
		if((int) $programType === Program::TYPE_PRIVATE_PROGRAM){
			$enrolment = Enrolment::findOne(['id' => $enrolmentId]);
			$enrolment->softDelete();
			
			$lessons = Lesson::find()
					->where(['enrolment_id' => $enrolmentId])
					->andWhere(['>', 'date', (new \DateTime())->format('Y-m-d H:i:s')])
					->all();
			foreach($lessons as $lesson){
				$lesson->softDelete();
			}
		} else {
			$groupEnrolment = GroupEnrolment::findOne(['id' => $enrolmentId]);
			$groupEnrolment->softDelete();
		}
 		Yii::$app->session->setFlash('alert', [
            'options' => ['class' => 'alert-success'],
            'body' => 'Enrolment has been deleted successfully'
        ]);
            return $this->redirect(['view', 'id' => $studentId,'#' => 'enrolment']);
    }

	public function actionDelete($id)
    {
        $model = $this->findModel($id);
		$enrolments = Enrolment::findAll(['student_id' => $model->id]);
		if( ! empty($enrolments)){
			foreach($enrolments as $enrolment){
				$lessons = Lesson::find()
						->where(['enrolment_id' => $enrolment->id])
						->andWhere(['>', 'date', (new \DateTime())->format('Y-m-d H:i:s')])
						->all();
				foreach($lessons as $lesson){
					$lesson->softDelete();
				}
				$enrolment->softDelete();
			}
		}
		
		$groupEnrolments = GroupEnrolment::findAll(['student_id' => $model->id]);
		if( ! empty($groupEnrolments)){
			foreach($groupEnrolments as $groupEnrolment){
				$groupEnrolment->softDelete();
			}
		}
		$model->softDelete();
		
 		Yii::$app->session->setFlash('alert', [
            'options' => ['class' => 'alert-success'],
            'body' => 'Student has been deleted successfully'
        ]);
            return $this->redirect(['index','StudentSearch[showAllStudents]' => 0]);
    }

	public function actionDeleteEnrolmentPreview($studentId, $enrolmentId, $programType)
    {
		$model = $this->findModel($studentId);
		if((int) $programType === Program::TYPE_PRIVATE_PROGRAM){
			$enrolmentModel = Enrolment::findOne(['student_id' => $studentId]); 
		} else {
			$enrolmentModel = GroupEnrolment::findOne(['student_id' => $studentId]); 
		}
        return $this->render('delete-enrolment-preview', [
			'model' => $model,
			'enrolmentId' => $enrolmentId,
			'programType' => $programType,
			'enrolmentModel' => $enrolmentModel,
        ]);
    }

	public function actionDeletePreview($id)
    {
		$model = $this->findModel($id);
	
		$enrolments = Enrolment::findAll(['student_id' => $model->id]);
		$groupEnrolments = GroupEnrolment::findAll(['student_id' => $model->id]);
        return $this->render('delete-preview', [
			'model' => $model,
			'enrolments' => $enrolments,
			'groupEnrolments' => $groupEnrolments
        ]);
    }

    /**
     * Finds the Student model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Student the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
	protected function findModel($id) {
		$session = Yii::$app->session;
		$locationId = $session->get('location_id');
		$model = Student::find()->joinWith(['customer' => function($query) use($locationId){
				$query->joinWith(['location' => function($query) use($locationId) {
						$query->where(['location_id' => $locationId]);
					}]);
				}])
			->where(['student.id' => $id])->one();
				if ($model !== null) {
					return $model;
				} else {
					throw new NotFoundHttpException('The requested page does not exist.');
				}
			}
	}
