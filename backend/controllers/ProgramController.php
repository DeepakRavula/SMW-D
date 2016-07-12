<?php

namespace backend\controllers;

use Yii;
use common\models\Program;
use common\models\Student;
use common\models\User;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\search\ProgramSearch;

/**
 * ProgramController implements the CRUD actions for Program model.
 */
class ProgramController extends Controller
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
     * Lists all Program models.
     * @return mixed
     */
    public function actionIndex()
    {   
        $searchModel = new ProgramSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Program model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
		
		$query = Student::find()
				->joinWith('enrolment')
				->where(['program_id' => $id]);
		
		$studentDataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$query = User::find()
				->joinWith('qualification')
				->where(['program_id' => $id]);
		$teacherDataProvider = new ActiveDataProvider([
			'query' => $query,
		]);
		
        return $this->render('view', [
            'model' => $this->findModel($id),
			'studentDataProvider' => $studentDataProvider, 
			'teacherDataProvider' => $teacherDataProvider, 
        ]);
    }

    /**
     * Creates a new Program model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Program();
		$model->status = Program::STATUS_ACTIVE;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
			Yii::$app->session->setFlash('alert', [
        	    'options' => ['class' => 'alert-success'],
            	'body' => 'Program has been created successfully'
        ]);
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Program model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
			
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
			if($model->status == Program::STATUS_INACTIVE){
				Yii::$app->session->setFlash('alert', [
            	'options' => ['class' => 'alert-success'],
            	'body' => 'Program has been updated successfully'
       		]);
				return $this->redirect(['index']);
			}
			else{
				Yii::$app->session->setFlash('alert', [
            	'options' => ['class' => 'alert-success'],
            	'body' => 'Program has been updated successfully'
        	]);
				return $this->redirect(['view', 'id' => $model->id]);
			}
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Program model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
		Yii::$app->session->setFlash('alert', [
           	'options' => ['class' => 'alert-success'],
           	'body' => 'Program has been deleted successfully'
        ]);
        return $this->redirect(['index']);
    }

    /**
     * Finds the Program model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Program the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Program::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
