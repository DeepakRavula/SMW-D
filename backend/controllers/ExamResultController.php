<?php

namespace backend\controllers;

use Yii;
use common\models\Student;
use common\models\ExamResult;
use common\models\User;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\helpers\Url;
use common\models\ExamResultLog;

/**
 * ExamResultController implements the CRUD actions for ExamResult model.
 */
class ExamResultController extends Controller
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
     * Lists all ExamResult models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => ExamResult::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ExamResult model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new ExamResult model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($studentId)
    {   
        
        $response = Yii::$app->response;
		$response->format = Response::FORMAT_JSON;  
                $model = new ExamResult();
                $userModel = User::findOne(['id' => Yii::$app->user->id]);
                $model->on(ExamResult::EVENT_CREATE, [new ExamResultLog(), 'create']);      
        $model->userName = $userModel->publicIdentity;
               if ($model->load(Yii::$app->request->post())) {
			$model->studentId = $studentId;
			if ($model->validate()) {
	            $model->save();
				$response = [
					'status' => true,
				];
			} else {
				$errors = ActiveForm::validate($model);
				$response = [
					'status' => false,
					'errors' =>  $errors
				];
			}
                        
                        
                        
                        
			return $response;
        }
        
             
        
        
    }

    /**
     * Updates an existing ExamResult model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
		$response = Yii::$app->response;
		$response->format = Response::FORMAT_JSON;
        $model = $this->findModel($id);
 $userModel = User::findOne(['id' => Yii::$app->user->id]);
                $model->on(ExamResult::EVENT_UPDATE, [new ExamResultLog(), 'edit']);      
        $model->userName = $userModel->publicIdentity;
		$data =  $this->renderAjax('//student/exam-result/_form', [
			'model' => $model,
		]);
		if ($model->load(Yii::$app->request->post())) {
	       	$model->save();
			return  [
				'status' => true,
			];
        }
		return [
			'status' => true,
			'data' => $data
		];

    }

    /**
     * Deletes an existing ExamResult model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
		$response = Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
		
		$model = $this->findModel($id);
        if($model->delete()) {
			$url = Url::to(['student/view', 'id' => $model->studentId, '#' => 'exam-result']);
        	return [
				'status' => true,
				'url' => $url,
			];
		}
    }

    /**
     * Finds the ExamResult model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return ExamResult the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ExamResult::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

	public function actionPrint($studentId)
    {
		$studentModel = Student::findOne(['id' => $studentId]);
        $examResults = ExamResult::find()->where(['studentId' => $studentId]);
        $examResultDataProvider = new ActiveDataProvider([
            'query' => $examResults,
        ]);

        $this->layout = '/print';

        return $this->render('/student/exam-result/_print', [
			'studentModel' => $studentModel,
			'examResultDataProvider' => $examResultDataProvider,
        ]);
	}
        
        
        
        
}
