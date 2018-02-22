<?php

namespace backend\controllers;

use Yii;
use common\models\LocationAvailability;
use yii\filters\ContentNegotiator;
use yii\web\Response;
use common\models\Location;
use yii\helpers\Url;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\widgets\ActiveForm;
use common\components\controllers\BaseController;
use yii\filters\AccessControl;

/**
 * LocationController implements the CRUD actions for Location model.
 */
class LocationController extends BaseController
{
    public function behaviors()
    {
        return [
            'contentNegotiator' => [
               'class' => ContentNegotiator::className(),
               'only' => ['create', 'update', 'edit-availability', 'add-availability', 'render-events', 'check-availability', 'validate',
                   'delete-availability','modify'],
                'formatParam' => '_format',
                'formats' => [
                   'application/json' => Response::FORMAT_JSON
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['update','view','validate', 'add-availability', 'edit-availability', 'delete-availability', 'render-events', 'check-availability','modify'],
                        'roles' => ['manageLocations']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index', 'delete', 'create'],
                        'roles' => ['administrator']
                    ]
                ]
            ] 
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
    public function actionView()
    {   
        $location = Location::findOne(['slug' => Yii::$app->location]);
        return $this->render('view', [
            'model' => $this->findModel($location->id),
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
        $data  = $this->renderAjax('_form', [
            'model' => $model,
        ]);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(Url::to(['/location-view', 'location' => $model->slug]));
        } else {
            return [
                'status' => true,
                'data' => $data
            ];
        }
    }
    public function actionValidate()
    {
        $model = new Location();
        
        $request = Yii::$app->request;
        if ($model->load($request->post())) {
            return  ActiveForm::validate($model);
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
    public function actionUpdate()
    {
        $location = Location::findOne(['slug' => Yii::$app->location]);
        $model = $this->findModel($location->id);
        $model->royaltyValue = $model->royalty->value;
        $model->advertisementValue = $model->advertisement->value;
        $data = $this->renderAjax('_form-update', [
            'model' => $model,
        ]);
        if ($model->load(Yii::$app->request->post())) {
            $model->royalty->value = $model->royaltyValue;
            $model->advertisement->value = $model->advertisementValue;
            $model->save();
            $model->royalty->save();
            $model->advertisement->save();
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
    
    
    public function actionDeleteAvailability($resourceId,$type)
    {
        $location = Location::findOne(['slug' => Yii::$app->location]);
        $availabilityModel = LocationAvailability::find()
            ->where(['locationId' => $location->id, 'day' => $resourceId, 'type' => $type])
            ->one();
        if($availabilityModel->delete()) {
            $response= [
                'status' => true
            ];
        } else {
            $response= [
                'status' => false,
            ];
        }
        return $response;
    }

    public function actionRenderEvents($type)
    {
        $location = Location::findOne(['slug' => Yii::$app->location]);
        $availabilities= LocationAvailability::find()
                ->andWhere(['locationId' => $location->id ,'type' => $type])
                ->all();
        $events = [];
        foreach ($availabilities as $availability) {
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

        return $this->redirect(['index', 'location' => $this->findModel(1)->slug]);
    }
      public function actionModify($resourceId,$type,$startTime,$endTime)
    {
          
           $location = Location::findOne(['slug' => Yii::$app->location]);
           $post     = Yii::$app->request->post();
           $model    = LocationAvailability::find()
                       ->where(['locationId' => $location->id, 'day' => $resourceId,'type' => $type])
                       ->one();
           
         if (empty($model)) { 
             $model=new LocationAvailability;
             $model->locationId=$location->id;
             $model->day=$resourceId;
             $model->fromTime=(new \DateTime($startTime))->format('g:i a');
             $model->toTime=(new \DateTime($endTime))->format('g:i a');
             $model->type=$type;
         }
         $model->fromTime=(new \DateTime($startTime))->format('g:i a');
         $model->toTime=(new \DateTime($endTime))->format('g:i a');
        $data =  $this->renderAjax('/location/_form-location-availability', [
            'model' => $model,
        ]);
        if ($post) {   
        if($model->load($post)) {
            $model->fromTime = (new \DateTime($model->fromTime))->format('H:i:s');
            $model->toTime   = (new \DateTime($model->toTime))->format('H:i:s');
            $model->save();
            return[
                'status' => true,
            ];
        } else {
            return [
                    'status' => false,
                    'errors' =>$model->getErrors()
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
}
