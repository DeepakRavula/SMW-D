<?php

namespace backend\controllers;

use Yii;
use common\models\ExamResult;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

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
     * Deletes an existing ExamResult model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
		$model = $this->findModel($id);
        $model->delete();

        return $this->redirect(['student/view', 'id' => $model->studentId, '#' => 'exam-result']);
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
