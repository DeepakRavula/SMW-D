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
		$privateLessons = Enrolment::find()
				->where(['student_id' => $id,'location_id' =>Yii::$app->session->get('location_id')])
				->all();

		$groupCourses = GroupCourse::find()
				->joinWith('groupEnrolments')
				->where(['student_id' => $model->id,'location_id' =>Yii::$app->session->get('location_id')])
        		->all();
		
		$session = Yii::$app->session;
		$location_id = $session->get('location_id');
		$currentDate = new \DateTime();
		$query = Lesson::find()
				->joinWith(['enrolment' => function($query) use($location_id,$id){
					$query->where(['location_id' => $location_id,'student_id' => $id]);
				}]);
		$lessonDataProvider = new ActiveDataProvider([
			'query' => $query,
		]);	

		$enrolmentModel = new Enrolment();
        $lessonModel = new Lesson();
        if($lessonModel->load(Yii::$app->request->post()) ){
           $studentEnrolmentModel = Enrolment::findOne(['student_id' => $id,'program_id' => $lessonModel->program_id]);
           $lessonModel->enrolment_id = $studentEnrolmentModel->id; 
           $lessonModel->status = Lesson::STATUS_SCHEDULED;
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
			$enrolmentModel->save();
			    Yii::$app->session->setFlash('alert', [
            	    'options' => ['class' => 'alert-success'],
                	'body' => 'Program has been added successfully'
            ]);
            	return $this->redirect(['view', 'id' => $model->id,'#' => 'enrolment']);
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
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
 		Yii::$app->session->setFlash('alert', [
            'options' => ['class' => 'alert-success'],
            'body' => 'Student profile has been deleted successfully'
        ]);
        return $this->redirect(['index']);
    }

	public function actionDeleteConfirm($programType, $studentId)
    {
		$model = $this->findModel($studentId);
		if($programType === Program::TYPE_PRIVATE_PROGRAM){
			$enrolmentModel = Enrolment::findOne(['student_id' => $studentId]); 
		} else {
			$enrolmentModel = GroupEnrolment::findOne(['student_id' => $studentId]); 
		}
        return $this->render('delete-confirm', [
			'model' => $model,
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
