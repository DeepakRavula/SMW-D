<?php

namespace backend\controllers;

use Yii;
use common\models\ClassroomUnavailability;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;
/**
 * ClassroomUnavailabilityController implements the CRUD actions for ClassroomUnavailability model.
 */
class ClassroomUnavailabilityController extends Controller
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
			[
				'class' => 'yii\filters\ContentNegotiator',
				'only' => ['create'],
				'formats' => [
					'application/json' => Response::FORMAT_JSON,
				],
        	],
        ];
    }

    /**
     * Lists all ClassroomUnavailability models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => ClassroomUnavailability::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ClassroomUnavailability model.
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
     * Creates a new ClassroomUnavailability model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($classroomId)
    {
        $model = new ClassroomUnavailability();

        if ($model->load(Yii::$app->request->post())) {
			$model->classroomId = $classroomId;
			if ($model->save()) {
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
     * Updates an existing ClassroomUnavailability model.
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
     * Deletes an existing ClassroomUnavailability model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ClassroomUnavailability model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return ClassroomUnavailability the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ClassroomUnavailability::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
