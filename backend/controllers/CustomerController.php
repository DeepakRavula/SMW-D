<?php

namespace backend\controllers;

use common\models\Payment;
use Yii;
use yii\helpers\Url;
use common\models\Location;
use common\models\User;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\Student;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\filters\AccessControl;
use common\models\OpeningBalance;
use backend\models\search\CustomerSearch;
use common\models\log\UserLog;
use yii\data\ActiveDataProvider;
use common\models\Enrolment;
/**
 * UserController implements the CRUD actions for User model.
 */
class CustomerController extends UserController
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
                'only' => ['merge', 'add-opening-balance', 'merge-preview'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
			'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['add-opening-balance', 'merge',  'merge-preview'],
                        'roles' => ['manageCustomers'],
                    ],
                ],
            ],
        ];
    }

    public function actionAddOpeningBalance($id)
    {
        $model = $this->findModel($id);
        $openingBalanceModel = new OpeningBalance();
        $openingBalanceModel->user_id = $model->id;
        $data       = $this->renderAjax('/user/customer/_form-opening-balance', [
            'userModel' => $model,
            'model' => $openingBalanceModel
        ]);
        $post = Yii::$app->request->post();
        if ($post) {
            if ($openingBalanceModel->load($post) && $openingBalanceModel->validate()) {
                $invoice = $openingBalanceModel->addOpeningBalance();
                $response = [
                    'status' => true,
                    'url' => Url::to(['invoice/view', 'id' => $invoice->id])
                ];
            } else {
                $response = [
                    'status' => false
                ];
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
        $locationId = Location::findOne(['slug' => Yii::$app->location])->id;
        $model = User::find()
            ->location($locationId)
            ->andWhere(['user.id' => $id])
            ->notDeleted()
            ->one();
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionMerge($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        $model->setScenario(User::SCENARIO_MERGE);
        $customerSearchModel = new CustomerSearch();
        $customerSearchModel->isStudentMerge = true;
        $customerSearchModel->customerId = $model->id;
        $customerDataProvider = $customerSearchModel->search($request->getQueryParams());
        $customerDataProvider->pagination = false;
        $customerId = Yii::$app->request->get('customerId');
        $data       = $this->renderAjax('/user/customer/_list', [
            'model' => $model,
            'customerDataProvider' => $customerDataProvider,
            'searchModel' => $customerSearchModel,
        ]);
        if ($customerId) {
            $model->customerId = $customerId;
            if ($model->validate()) {
                    $customer = User::findOne($model->customerId);
                    foreach ($customer->students as $student) {
                        $student->setScenario(Student::SCENARIO_CUSTOMER_MERGE);
                        $student->customer_id = $id;
                        $student->save();
                    }
                    foreach ($customer->notes as $note) {
                        $note->instanceId = $id;
                        $note->save();
                    }
                    foreach ($customer->logs as $log) {
                        $logHistory = $log->logHistory;
                        $logHistory->instanceId = $id;
                        $logHistory->save();
                    }
                    $customer->on(
                        User::EVENT_AFTER_MERGE,
                        [new UserLog(), 'afterCustomerMerge']
                    );
                    $customer->trigger(User::EVENT_AFTER_MERGE);
                return [
                    'status' => true,
                    'message' => 'Customer successfully merged!'
                ];
            } else {
                $errors = ActiveForm::validate($model);
                return [
                    'status' => false,
                    'errors' => current($errors)
                ];
            }
        } else {
            return [
                'status' => true,
                'data' => $data
            ];
        }
    }

    public function actionMergePreview()
    {
        $id = Yii::$app->request->get('id');
        $customerId = Yii::$app->request->get('customerId');
        $model = $this->findModel($id);
        $model->setScenario(User::SCENARIO_MERGE);
        $mergeUserModel = $this->findModel($customerId);
        $paymentRequestDataProvider = $mergeUserModel->paymentRequests;
        $locationId = Location::findOne(['slug' => Yii::$app->location])->id;    
        $data       = $this->renderAjax('/user/customer/_merge-preview', [
            'model' => $model,
            'enrolmentDataProvider' => $this->getEnrolmentDataProvider($mergeUserModel->id, $locationId),
            'mergeUserModel' => $mergeUserModel,
            'paymentRequestDataProvider' => $paymentRequestDataProvider,
        ]);
        return [
                'status' => true,
                'data' => $data
            ];
    }

    protected function getEnrolmentDataProvider($id, $locationId)
    {
        $currentdate = new \DateTime();
        $currentDate = $currentdate->format('Y-m-d');
        $enrolmentQuery = Enrolment::find()
            ->joinWith(['student' => function ($query) use ($id) {
                $query->andWhere(['customer_id' => $id]);
            }])
            ->notDeleted()
            ->isConfirmed()
            ->isRegular()
            ->location($locationId)
            ->groupBy(['enrolment.id'])
            ->active();

        return new ActiveDataProvider([
            'query' => $enrolmentQuery,
        ]);
    }
}
