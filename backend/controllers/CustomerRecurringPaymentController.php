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
use common\models\User;
use common\models\log\CustomerRecurringPaymentLog;
use common\models\Location;
use Carbon\Carbon;
use yii\bootstrap\ActiveForm;
use phpDocumentor\Reflection\Types\Null_;
use common\models\RecurringPayment;
use backend\models\search\CustomerRecurringPaymentSearch;

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
        //$locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $searchModel = new CustomerRecurringPaymentSearch();
        $request = Yii::$app->request;
        $dataProvider = $searchModel->search($request->queryParams);
        // $recurringPayments = CustomerRecurringPayment::find()
        //         ->notDeleted()
        //         ->location($locationId);
        // $recurringPaymentDataProvider  = new ActiveDataProvider([
        //     'query' =>  $recurringPayments,
        // ]);
        return $this->render('index', [
                'dataProvider' => $dataProvider
            ]);
    }

    public function actionCreate($id = null)
    {
        $post = Yii::$app->request->post();
        $get =  Yii::$app->request->get();
        
        $currentDate = Carbon::now();
        $model = new CustomerRecurringPayment();
        $model->paymentMethodId = 8;
        $model->load($get);
        $enrolment = Enrolment::find()
        ->notDeleted()
        ->customer($model->customerId)
        ->recurringPaymentExcluded() 
        ->privateProgram()
        ->notCompleted()
        ->isConfirmed();
    
$enrolmentDataProvider  = new ActiveDataProvider([
'query' => $enrolment,
'pagination' => false,
]);
        $customerRecurringPaymentEnrolmentModel =  new CustomerRecurringPaymentEnrolment();
        $data = $this->renderAjax('_form', [
            'model' => $model,
            'enrolmentDataProvider' => $enrolmentDataProvider,
            'customerRecurringPaymentEnrolment' => $customerRecurringPaymentEnrolmentModel,
        ]);
        if ($post) {
        if ($model->load(Yii::$app->request->post())) {
            if ($model->expiryMonth && $model->expiryYear) {
                $expiryDate = (new \DateTime())->format('d') . '-' . $model->expiryMonth . '-' . $model->expiryYear;
                $lastDate = (new \DateTime($expiryDate))->format('t');
                $expiryDate = $lastDate . '-' . $model->expiryMonth . '-' . $model->expiryYear;
                $model->expiryDate = (new \DateTime($expiryDate))->format('Y-m-d');
            }
            $startDate = Carbon::parse($model->startDate);
            $currentDate = Carbon::now()->format('Y-m-d');
            $model->startDate = Carbon::parse($model->startDate)->format('Y-m-d');
            $model->entryDay = Carbon::parse($startDate)->format('d');
            if (Carbon::parse($model->startDate)->format('Y-m-d') >= $currentDate ) {
                $model->nextEntryDay = Carbon::parse($model->startDate)->format('Y-m-d');
            } else {
                $model->nextEntryDay = Carbon::parse($model->startDate)->addMonthsNoOverflow($model->paymentFrequencyId)->format('Y-m-d');
            }
            $loggedUser = User::findOne(['id' => Yii::$app->user->id]);
                 $model->on(CustomerRecurringPayment::EVENT_AFTER_INSERT, [new CustomerRecurringPaymentLog(), 'customerRecurringPaymentCreate'],
                    ['loggedUser' => $loggedUser,]
                 );
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
            $oldStartDate = Carbon::parse($model->startDate)->format('Y-m-d');
            if ($model->load(Yii::$app->request->post())) {
                if ($model->expiryMonth && $model->expiryYear) {
                    $expiryDate = (new \DateTime())->format('d') . '-' . $model->expiryMonth . '-' . $model->expiryYear;
                    $lastDate = (new \DateTime($expiryDate))->format('t');
                    $expiryDate = $lastDate . '-' . $model->expiryMonth . '-' . $model->expiryYear;
                    $model->expiryDate = (new \DateTime($expiryDate))->format('Y-m-d');
                }
                $model->startDate = Carbon::parse($model->startDate)->format('Y-m-d');
                $currentDate = Carbon::now()->format('Y-m-d');
                if ($oldStartDate != Carbon::parse($model->startDate)->format('Y-m-d')) {
                if (Carbon::parse($model->startDate)->format('Y-m-d') >= $currentDate ) {
                    $model->nextEntryDay = Carbon::parse($model->startDate)->format('Y-m-d');
                } else {
                    $startDate = Carbon::parse($currentDate)->subMonthsNoOverflow($model->paymentFrequencyId - 1)->format('Y-m-1');
                    $endDate = $currentDate;
                    $previousRecordedPayment = RecurringPayment::find()
                        ->andWhere(['customerRecurringPaymentId' => $model->id])
                        ->orderBy(['recurring_payment.date' => SORT_DESC]);
                    $recentRecordedPayment = $previousRecordedPayment->between($startDate, $endDate)->one();
                    if (!$recentRecordedPayment) {
                        $previousRecordedPaymentAny = $previousRecordedPayment->one();
                        if ($previousRecordedPaymentAny) {
                            $nextEntryDay = Carbon::parse($previousRecordedPayment->date)->addMonthsNoOverflow($model->paymentFrequencyId)->format('Y-m-d');
                        } else {
                            $nextEntryDayDate = Carbon::parse($model->startDate)->format('d');
                            $nextEntryDayMonth = Carbon::parse($currentDate)->format('m');
                            $nextEntryDayYear = Carbon::parse($currentDate)->format('Y');
                            $nextEntryDay = Carbon::parse($nextEntryDayYear . '-' . $nextEntryDayMonth . '-' . $nextEntryDayDate)->format('Y-m-d');
                        }
                        while (Carbon::parse($nextEntryDay) <= Carbon::parse($currentDate)->format('Y-m-d')) {
                            $nextEntryDay = Carbon::parse($nextEntryDay)->addMonthsNoOverflow($model->paymentFrequencyId)->format('Y-m-d');
                        }
                        $model->nextEntryDay = $nextEntryDay;
                    }
                }
            }
                $loggedUser = User::findOne(['id' => Yii::$app->user->id]);
                 $model->on(CustomerRecurringPayment::EVENT_AFTER_UPDATE, [new CustomerRecurringPaymentLog(), 'customerRecurringPaymentEdit'],
                    ['loggedUser' => $loggedUser,]
                 );
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