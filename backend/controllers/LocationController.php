<?php

namespace backend\controllers;

use Yii;
use common\models\LocationAvailability;
use yii\filters\ContentNegotiator;
use yii\web\Response;
use common\models\Location;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * LocationController implements the CRUD actions for Location model.
 */
class LocationController extends Controller
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
               'only' => ['edit-availability', 'add-availability', 'render-events', 'check-availability',
                   'delete-availability'],
               'formatParam' => '_format',
               'formats' => [
                   'application/json' => Response::FORMAT_JSON,
               ],
           ],
        ];
    }

    /**
     * Lists all Location models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Location::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Location model.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('_view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Location model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Location();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('alert', [
                'options' => ['class' => 'alert-success'],
                'body' => 'Location has been created successfully',
        ]);

            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Location model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model  = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('alert', [
                'options' => ['class' => 'alert-success'],
                'body' => 'Location has been updated successfully',
        ]);

            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('_form', [
                'model' => $model,
            ]);
        }
    }

    public function actionEditAvailability($id, $resourceId, $startTime, $endTime)
    {
        $availabilityModel = LocationAvailability::find()
            ->where(['locationId' => $id, 'day' => $resourceId])
            ->one();
        $availabilityModel->fromTime = $startTime;
        $availabilityModel->toTime = $endTime;
        return $availabilityModel->save();
    }

    public function actionDeleteAvailability($id, $resourceId)
    {
        $availabilityModel = LocationAvailability::find()
            ->where(['locationId' => $id, 'day' => $resourceId])
            ->one();
        return $availabilityModel->delete();
    }

    public function actionAddAvailability($id, $resourceId, $startTime, $endTime)
    {
        $model = new LocationAvailability();
        $model->locationId = $id;
        $model->day = $resourceId;
        $model->fromTime = $startTime;
        $model->toTime = $endTime;
        return $model->save();
    }

    public function actionRenderEvents($id)
    {
        $model  = $this->findModel($id);
        $events = [];
        foreach ($model->locationAvailabilities as $availability) {
            $startTime = new \DateTime($availability->fromTime);
            $endTime   = new \DateTime($availability->toTime);
            $events[] = [
                'resourceId' => $availability->day,
                'start' => $startTime->format('Y-m-d H:i:s'),
                'end' => $endTime->format('Y-m-d H:i:s'),
                'backgroundColor' => '#ffffff',
                'className' => 'location-availability',
            ];
        }

        return $events;
    }

    public function actionCheckAvailability($id, $resourceId)
    {
        $availabilityModel = LocationAvailability::find()
            ->where(['locationId' => $id, 'day' => $resourceId])
            ->one();
        $response = [
            'status' => true,
        ];
        if(!empty($availabilityModel)) {
            $response = [
                'status' => false,
            ];
        }

        return $response;
    }

    /**
     * Deletes an existing Location model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('alert', [
               'options' => ['class' => 'alert-success'],
               'body' => 'Location has been deleted successfully',
        ]);

        return $this->redirect(['index']);
    }

    /**
     * Finds the Location model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param int $id
     *
     * @return Location the loaded model
     *
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Location::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionChangeLocation()
    {
        if (Yii::$app->request->isAjax) {
            $location_id = Yii::$app->request->post('location_id');
            Yii::$app->session->set('location_id', $location_id);
        }
    }
}
