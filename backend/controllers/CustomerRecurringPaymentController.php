<?php

namespace backend\controllers;

use Yii;
use common\models\CustomerRecurringPaymentEnrolment;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\filters\ContentNegotiator;
use yii\web\Response;
use yii\helpers\Url;
use common\models\Enrolment;
use common\models\CustomerRecurringPayment;
use common\models\Location;
use Carbon\Carbon;
use yii\bootstrap\ActiveForm;

class CustomerRecurringPaymentController extends \common\components\controllers\BaseController
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
                'only' => ['create', 'update', 'delete'],
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
                        'actions' => ['create', 'update', 'index', 'delete'],
                        'roles' => [
                            'managePfi'
                       ]
                    ],
                ],
            ],
        ];
    }


    public function actionIndex()
    {
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $recurringPayments = CustomerRecurringPayment::find()
                ->notDeleted()
                ->location($locationId);
        $recurringPaymentDataProvider  = new ActiveDataProvider([
            'query' =>  $recurringPayments,
        ]);
        return $this->render('index', [
                'dataProvider' => $recurringPaymentDataProvider
            ]);
    }

    public function actionCreate($id)
    {
        $enrolment = Enrolment::find()
                    ->notDeleted()
                    ->customer($id)
                    ->recurringPaymentExcluded() 
                    ->privateProgram()
                    ->notCompleted()
                    ->isConfirmed();
                
        $enrolmentDataProvider  = new ActiveDataProvider([
            'query' => $enrolment,
            'pagination' => false,
        ]);
        $post = Yii::$app->request->post();
        $get =  Yii::$app->request->get();
        
        $model = new CustomerRecurringPayment();
        $model->customerId = $id;
        $customerRecurringPaymentEnrolmentModel =  new CustomerRecurringPaymentEnrolment();
        $data = $this->renderAjax('_form', [
            'model' => $model,
            'enrolmentDataProvider' => $enrolmentDataProvider,
            'customerRecurringPaymentEnrolment' => $customerRecurringPaymentEnrolmentModel,
        ]);
        if ($post) {
        if ($model->load(Yii::$app->request->post())) {
            $model->expiryDate = (new \DateTime($model->expiryDate))->format('Y-m-d');
            $startDate = Carbon::parse($model->startDate);
            $model->startDate = Carbon::parse($model->startDate)->format('Y-m-d');
            $model->entryDay = Carbon::parse($startDate)->format('d');
            if($model->save()) {
                  $customerRecurringPaymentEnrolmentModel->load($get);
                  if ($customerRecurringPaymentEnrolmentModel->enrolmentIds) {
                  foreach ($customerRecurringPaymentEnrolmentModel->enrolmentIds as $enrolmentId) {
                      $customerRecurringPaymentEnrolment = new CustomerRecurringPaymentEnrolment();
                      $customerRecurringPaymentEnrolment->enrolmentId = $enrolmentId;
                      $customerRecurringPaymentEnrolment->customerRecurringPaymentId = $model->id;
                      $customerRecurringPaymentEnrolment->save();
                  } 
                }
                  return [
                    'status' => true
                ];
            }
           
         else {
            return [
                    'status' => false,
                    'errors' => ActiveForm::validate($model)
                ];
            }
        }
    }
            else {
            return [
                'status' => true,
                'data' => $data
            ];
    }
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $enrolments = Enrolment::find()
                ->notDeleted()
                ->customer($model->customerId)
                ->anotherRecurringPaymentExcluded($model->id)
                ->privateProgram()
                ->notCompleted()
                ->isConfirmed();
                
        $enrolmentDataProvider  = new ActiveDataProvider([
            'query' => $enrolments,
            'pagination' => false,
        ]);
        $data = $this->renderAjax('_form-update', [
            'model' => $model,
            'enrolmentDataProvider' => $enrolmentDataProvider,
        ]);
        if (Yii::$app->request->post()) {
            if ($model->load(Yii::$app->request->post())) {
                $model->expiryDate = (new \DateTime($model->expiryDate))->format('Y-m-d');
                $model->startDate = Carbon::parse($model->startDate)->format('Y-m-d');
                if ($model->save()) {
                    $customerRecurringPaymentEnrolments = CustomerRecurringPaymentEnrolment::find()
                                                        ->notDeleted()
                                                        ->andWhere(['customerRecurringPaymentId' => $model->id])  
                                                        ->all();
                    foreach ($customerRecurringPaymentEnrolments as $customerRecurringPaymentEnrolment) {
                        $customerRecurringPaymentEnrolment->delete();
                    }
                    $customerRecurringPaymentEnrolmentModel = new CustomerRecurringPaymentEnrolment();
                    $customerRecurringPaymentEnrolmentModel->load(Yii::$app->request->get());
                    if ($customerRecurringPaymentEnrolmentModel->enrolmentIds) {
                  foreach ($customerRecurringPaymentEnrolmentModel->enrolmentIds as $enrolmentId) {
                      $customerRecurringPaymentEnrolment = new CustomerRecurringPaymentEnrolment();
                      $customerRecurringPaymentEnrolment->enrolmentId = $enrolmentId;
                      $customerRecurringPaymentEnrolment->customerRecurringPaymentId = $model->id;
                      $customerRecurringPaymentEnrolment->save();
                } 
            }
                    $response = [
                        'status' => true
                    ];
                } else {
                    $response = [
                            'status' => false,
                            'errors' => ActiveForm::validate($model)
                        ];
                }
            }
        } else {
            $response = [
                'status' => true,
                'data' => $data
            ];
        }
        return $response;
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $customerRecurringPaymentEnrolments = CustomerRecurringPaymentEnrolment::find()
                ->notDeleted()
                ->andWhere(['customerRecurringPaymentId' => $id])
                ->all();
        foreach ($customerRecurringPaymentEnrolments as $customerRecurringPaymentEnrolment) {
            $customerRecurringPaymentEnrolment->delete();
        }
        $model->delete();
            $response = [
                'status' => true,
            ];
        return $response;
    }

    protected function findModel($id)
    {
        if (($model = CustomerRecurringPayment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}   