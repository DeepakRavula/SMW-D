<?php

namespace backend\controllers;

use Yii;
use common\models\CustomerPaymentPreference;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\User;
use yii\filters\ContentNegotiator;
use backend\models\search\CustomerPaymentPreferenceSearch;
use yii\filters\AccessControl;
use yii\web\Response;
use common\components\controllers\BaseController;
use yii\helpers\Url;

/**
 * CustomerDiscountController implements the CRUD actions for CustomerDiscount model.
 */
class CustomerPaymentPreferenceController extends BaseController
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
                'only' => ['modify', 'delete', 'customer-preferred-payment-status'],
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
                        'actions' => ['index', 'update', 'view', 'modify', 'delete', 'customer-preferred-payment-status'],
                        'roles' => ['manageCustomers'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Displays a single CustomerDiscount model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new CustomerDiscount model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CustomerPaymentPreferenceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionModify($id)
    {
        $userModel = User::findOne($id);
        if (empty($userModel->customerPaymentPreference)) {
            $model = new CustomerPaymentPreference();
            $model->userId = $id;
        } else {
            $model = $userModel->customerPaymentPreference;
            if($model->expiryDate) {
            $model->expiryDate = (new \DateTime($model->expiryDate))->format('M d,Y');
            }
        }
        $data = $this->renderAjax('/user/customer/_form-payment-preference', [
            'model' => $model,
            'userModel' => $userModel,
        ]);
        if (Yii::$app->request->post()) {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                $response = [
                    'status' => true
                ];
            } else {
                $errors = ActiveForm::validate($model);
                $response = [
                    'status' => false,
                    'errors' => $errors
                ];
            }
        } else {
            $response = [
                'status' => true,
                'data' => $data,
                'id' => $model->id
            ];
        }
        return $response;
    }

    /**
     * Updates an existing CustomerDiscount model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing CustomerDiscount model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();
            $response = [
                'status' => true,
                'url' => Url::to(['user/view', $model->id]),
            ];
        return $response;
    }

    /**
     * Finds the CustomerDiscount model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return CustomerDiscount the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CustomerPaymentPreference::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionCustomerPreferredPaymentStatus($state, $id)
    {
        $model = CustomerPaymentPreference::find()
                ->andWhere([
                    'id' => $id,
                ])
                ->notDeleted()
                ->one();
        $model->isPreferredPaymentEnabled = $state;
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
