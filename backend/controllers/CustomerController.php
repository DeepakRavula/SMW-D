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
                'only' => ['merge', 'add-opening-balance'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
			'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['add-opening-balance', 'merge'],
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
        $customerDataProvider = $customerSearchModel->search($request->getQueryParams());
        $customerDataProvider->pagination = false;
        $data       = $this->renderAjax('/user/customer/_list', [
            'model' => $model,
            'customerDataProvider' => $customerDataProvider,
            'searchModel' => $customerSearchModel,
        ]);
        $post = Yii::$app->request->post();
        
        if ($model->load($post)) {
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
                        $log->userId = $id;
                        $log->save();
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
}
