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
                'only' => ['create', 'update', 'customer-recurring-payment-status'],
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
                        'actions' => ['create', 'update', 'customer-recurring-payment-status'],
                        'roles' => [
                            'managePfi'
                       ]
                    ],
                ],
            ],
        ];
    }


    public function actionCreate($id)
    {
        $enrolment = Enrolment::find()
                    ->notDeleted()
                    ->customer($id)
                    ->recurringPaymentExcluded() 
                    ->privateProgram()
                    ->active()
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
            if($model->save()) {
                  $customerRecurringPaymentEnrolmentModel->load($get);
                  foreach ($customerRecurringPaymentEnrolmentModel->enrolmentIds as $enrolmentId) {
                      $customerRecurringPaymentEnrolment = new CustomerRecurringPaymentEnrolment();
                      $customerRecurringPaymentEnrolment->enrolmentId = $enrolmentId;
                      $customerRecurringPaymentEnrolment->customerRecurringPaymentId = $model->id;
                      $customerRecurringPaymentEnrolment->save();
                  } 
                  return [
                    'status' => true
                ];
            }
           
         else {
            return [
                    'status' => false,
                    'errors' => $model->getErrors()
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
                ->active()
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
                if ($model->save()) {
                    $customerRecurringPaymentEnrolments = CustomerRecurringPaymentEnrolment::find()
                                                          ->andWhere(['customerRecurringPaymentId' => $model->id])  
                                                           ->all();
                    foreach ($customerRecurringPaymentEnrolments as $customerRecurringPaymentEnrolment) {
                        $customerRecurringPaymentEnrolment->delete();
                    }
                    $customerRecurringPaymentEnrolmentModel = new CustomerRecurringPaymentEnrolment();
                    $customerRecurringPaymentEnrolmentModel->load(Yii::$app->request->get());
                  foreach ($customerRecurringPaymentEnrolmentModel->enrolmentIds as $enrolmentId) {
                      $customerRecurringPaymentEnrolment = new CustomerRecurringPaymentEnrolment();
                      $customerRecurringPaymentEnrolment->enrolmentId = $enrolmentId;
                      $customerRecurringPaymentEnrolment->customerRecurringPaymentId = $model->id;
                      $customerRecurringPaymentEnrolment->save();
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

    protected function findModel($id)
    {
        if (($model = CustomerRecurringPayment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionCustomerRecurringPaymentStatus($state, $id)
    {
        print_r($id);die('ss');
        $model = CustomerRecurringPayment::find()
                ->andWhere([
                    'id' => $id,
                ])
                ->notDeleted()
                ->one();
        $model->isRecurringPaymentEnabled = $state;
        if ($model->save()) {
            $response = [
                'status' => true,
            ]; 
        } else {
            $response = [
                'status' => false,
                'errors' =>$model->getErrors()
            ];
        }
        return $response;
    }
}   