<?php

namespace backend\controllers;

use Yii;
use common\models\Classroom;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\ClassroomUnavailability;
use yii\filters\ContentNegotiator;
use yii\widgets\ActiveForm;
use yii\web\Response;
use yii\filters\AccessControl;
use common\components\controllers\BaseController;
use common\models\Location;

/**
 * ClassRoomController implements the CRUD actions for Classroom model.
 */
class ClassroomController extends BaseController
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
                'only' => ['create', 'update'],
                'formatParam' => '_format',
                'formats' => [
                   'application/json' => Response::FORMAT_JSON,
                ],
            ],
			'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view', 'update', 'create', 'delete'],
                        'roles' => ['manageClassrooms'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Classroom models.
     * @return mixed
     */
    public function actionIndex()
    {
        $locationId = \common\models\Location::findOne(['slug' => \Yii::$app->location])->id;
        $dataProvider = new ActiveDataProvider([
            'query' => Classroom::find()
                ->andWhere(['locationId' => $locationId]),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Classroom model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        $unavailabilities = ClassroomUnavailability::find()
            ->andWhere(['classroomId' => $id])
            ->orderBy(['id' => SORT_DESC]);

        $unavailabilityDataProvider = new ActiveDataProvider([
            'query' => $unavailabilities,
        ]);
        
        return $this->render('view', [
            'model' => $this->findModel($id),
            'unavailabilityDataProvider' => $unavailabilityDataProvider
        ]);
    }

    /**
     * Creates a new Classroom model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Classroom();
        $data = $this->renderAjax('_form', [
            'model' => $model,
        ]);
        if (Yii::$app->request->post()) {
            $model->locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
            if($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return [
                        'status' => false,
                        'errors' =>ActiveForm::validate($model)
                    ];
                }
        } else {
            return [
                'status' => true,
                'data' => $data
            ];
        }
    }

    /**
     * Updates an existing Classroom model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
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
            } else {
                return [
                        'status' => false,
                        'errors' =>ActiveForm::validate($model)
                    ];
            }
        } else {
            return [
                'status' => true,
                'data' => $data
            ];
        }
    }

    /**
     * Deletes an existing Classroom model.
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
     * Finds the Classroom model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Classroom the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Classroom::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
