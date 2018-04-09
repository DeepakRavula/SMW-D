<?php

namespace backend\controllers;

use Yii;
use common\models\TestEmail;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\ContentNegotiator;
use yii\web\Response;
use yii\data\ActiveDataProvider;
/**
 * EmailTemaplateController implements the CRUD actions for EmailTemplate model.
 */
class TestEmailController extends \common\components\controllers\BaseController
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
                'only' => ['update'],
                'formatParam' => '_format',
                'formats' => [
                   'application/json' => Response::FORMAT_JSON,
                ],
            ]
        ];
    }

    /**
     * Lists all Province models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $testEmail = TestEmail::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $testEmail,
        ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single EmailTemplate model.
     *
     * @param int $id
     *
     * @return mixed
     */
    /**
     * Updates an existing EmailTemplate model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $data = $this->renderAjax('_form', [
            'model' => $model,
        ]);
        if (Yii::$app->request->post()) {
            if($model->load(Yii::$app->request->post()) && $model->save()) {
                return [
                    'status' => true
                ];
            } 
        else {
            return [
                    'status' => false,
                    'errors' =>$model->getErrors()
                ];
            }
        }
            else {
            return [
                'status' => true,
                'data' => $data
            ];
        }
    }
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if ($model->delete()) {
            return [
                'status' => true,
            ];
        }
    }

    /**
     * Finds the EmailTemplate model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param int $id
     *
     * @return Province the loaded model
     *
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TestEmail::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
