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
use yii\filters\ContentNegotiator;

/**
 * ExamResultController implements the CRUD actions for ExamResult model.
 */
class ExamResultController extends \common\components\controllers\BaseController
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
           'contentNegotiator' => [
                'class' => ContentNegotiator::className(),
                'only' => ['create'],
                'formatParam' => '_format',
                'formats' => [
                   'application/json' => Response::FORMAT_JSON,
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
        
                $model = new ExamResult();
			$model->studentId = $studentId;
			 $data  = $this->renderAjax('/student/exam-result/_form', [
            'model' => $model,
        ]); 
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return [
				'status' => true
			];
        } else {
            return [
                'status' => true,
                'data' => $data
            ];
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
            $model->trigger(ExamResult::EVENT_DELETE);
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
}
