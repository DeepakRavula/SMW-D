<?php

namespace backend\controllers;

use Yii;
use common\models\Qualification;
use backend\models\search\QualificationSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\filters\ContentNegotiator;
use yii\widgets\ActiveForm;

/**
 * QualificationController implements the CRUD actions for Qualification model.
 */
class QualificationController extends \common\components\controllers\BaseController
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
                'only' => ['update', 'delete', 'create', 'add-group'],
                'formatParam' => '_format',
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
        ];
    }

    /**
     * Lists all Qualification models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new QualificationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Qualification model.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Qualification model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate($id)
    {
        $model = new Qualification();

        if ($model->load(Yii::$app->request->post())) {
            $model->teacher_id = $id;
            $model->type = Qualification::TYPE_HOURLY;
            $model->isDeleted = false;
            if ($model->save()) {
                $response = [
                    'status' => true,
                ];
            } else {
                $response = [
                    'status' => false,
                    'errors' => ActiveForm::validate($model)
                ];
            }
            return $response;
        }
    }

    public function actionAddGroup($id)
    {
        $model = new Qualification();

        if ($model->load(Yii::$app->request->post())) {
            $model->teacher_id = $id;
            $model->type = Qualification::TYPE_FIXED;
            $model->isDeleted = false;
            if ($model->save()) {
                $response = [
                    'status' => true,
                ];
            } else {
                $response = [
                    'status' => false,
                    'errors' => ActiveForm::validate($model)
                ];
            }
            return $response;
        }
    }

    /**
     * Updates an existing Qualification model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $data = $this->renderAjax('update', [
            'model' => $model,
        ]);
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                $response = [
                    'status' => true,
                ];
            } else {
                $response = [
                    'status' => false,
                    'errors' => ActiveForm::validate($model)
                ];
            }
            return $response;
        } else {
            return [
                'status' => true,
                'data' => $data
            ];
        }
    }

    /**
     * Deletes an existing Qualification model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return [
            'status' => true,
        ];
    }

    /**
     * Finds the Qualification model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param string $id
     *
     * @return Qualification the loaded model
     *
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Qualification::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
