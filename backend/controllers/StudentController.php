<?php

namespace backend\controllers;

use Yii;
use common\models\Student;
use common\models\Enrolment;
use common\models\Lesson;
use common\models\Qualification;
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
		$session = Yii::$app->session;
        $dataProvider = new ActiveDataProvider([
            'query' => Student::find()
					->join('INNER JOIN','user','user.id = customer_id')
					->join('INNER JOIN','user_location','user_location.user_id = user.id')
					->where(['user_location.location_id' => $session->get('location_id') ])
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Student model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
		$dataProvider = new ActiveDataProvider([
            'query' => Enrolment::find()->where(['student_id' => $id,'location_id' =>Yii::$app->session->get('location_id')])
        ]);
		$lessonModel = new ActiveDataProvider([
			'query' => Lesson::find()
				->join('INNER JOIN','enrolment_schedule_day esd','esd.id = lesson.enrolment_schedule_day_id')
				->join('INNER JOIN','enrolment e','e.id = esd.enrolment_id')
				->join('INNER JOIN','student s','s.id = e.student_id')
				->where(['e.student_id' => $id,'e.location_id' => Yii::$app->session->get('location_id')])
				->andWhere('lesson.date <= NOW()')
		]);
        $model = $this->findModel($id);
		$enrolmentModel = new Enrolment();
        if ($enrolmentModel->load(Yii::$app->request->post()) ) {
			$enrolmentModel->student_id = $id;
			$qualification = Qualification::findOne([
				'teacher_id' => $enrolmentModel->teacherId,
				'program_id' => $enrolmentModel->programId,
			]);
			$enrolmentModel->qualification_id = $qualification->id;
			$enrolmentModel->location_id = Yii::$app->session->get('location_id');
			$renewalDate = \DateTime::createFromFormat('m-d-y', $enrolmentModel->commencement_date);
			$renewalDate->add(new \DateInterval('P1Y'));
			$enrolmentModel->renewal_date = $renewalDate->format('Y-m-d');
			if($enrolmentModel->save()) {
			    Yii::$app->session->setFlash('alert', [
            	    'options' => ['class' => 'alert-success'],
                	'body' => 'Program has been added successfully'
            ]);
            	return $this->redirect(['view', 'id' => $model->id]);
			}
        } else {
            return $this->render('view', [
            	'model' => $model,
            	'dataProvider' => $dataProvider,
                'enrolmentModel' => $enrolmentModel,
				'lessonModel' => $lessonModel,
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
            return $this->redirect(['user/view', 'UserSearch[role_name]' => $role, 'id' => $model->customer_id]);
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

    /**
     * Finds the Student model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Student the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Student::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
