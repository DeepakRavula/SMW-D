<?php

namespace backend\controllers;

use Yii;
use common\models\Student;
use common\models\Enrolment;
use common\models\Qualification;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

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
					->where(['user.location_id' => $session->get('location_id') ])
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
            'query' => Enrolment::find()->where(['student_id' => $id])
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
			if($enrolmentModel->save()) {
            	return $this->redirect(['view', 'id' => $model->id]);
			}
        } else {
            return $this->render('view', [
            	'model' => $model,
            	'dataProvider' => $dataProvider,
                'enrolmentModel' => $enrolmentModel,
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
		$session = Yii::$app->session;
		
        if ($model->load(Yii::$app->request->post())) {
			$model->customer_id = $session->get('customer_id');
			$model->save();
            return $this->redirect(['user/view', 'id' => $session->get('customer_id')]);
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
