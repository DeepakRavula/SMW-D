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
                        'actions' => ['create', 'update'],
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
                    ->isConfirmed();
                
        $enrolmentDataProvider  = new ActiveDataProvider([
            'query' => $enrolment,
            'pagination' => false,
        ]);
        $post = Yii::$app->request->post();
        $get =  Yii::$app->request->get();
        
        $model = new CustomerRecurringPayment();
        $customerRecurringPaymentEnrolmentModel =  new CustomerRecurringPaymentEnrolment();
        $data = $this->renderAjax('_form', [
            'model' => $model,
            'id' => $id,
            'enrolmentDataProvider' => $enrolmentDataProvider,
            'customerRecurringPaymentEnrolment' => $customerRecurringPaymentEnrolmentModel,
        ]);
        if ($post) {
        if ($model->load(Yii::$app->request->post())) {
            $model->customerId = $id;
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
        $enrolment = Enrolment::find()
                    ->notDeleted()
                    ->customer($model->customerId)
                    ->joinWith(['customerRecurringPaymentEnrolment' => function ($query) use ($id) {
                        $query->andWhere(['customerRecurringPaymentId' => $id]);
                    }]) 
                    ->isConfirmed();
                
        $enrolmentDataProvider  = new ActiveDataProvider([
            'query' => $enrolment,
            'pagination' => false,
        ]);
        $data = $this->renderAjax('_form', [
            'model' => $model,
            'id' => $model->customerId,
            'enrolmentDataProvider' => $enrolmentDataProvider,
        ]);
        if (Yii::$app->request->post()) {
            if($model->load(Yii::$app->request->post())) {
            $model->expiryDate = (new \DateTime($model->expiryDate))->format('Y-m-d');
                if ($model->save()) {
                    return [
                        'status' => true
                    ];
                } else {
                    return [
                            'status' => false,
                            'errors' =>$model->getErrors()
                        ];
                }
            }
        } else {
            return [
                'status' => true,
                'data' => $data
            ];
        }
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