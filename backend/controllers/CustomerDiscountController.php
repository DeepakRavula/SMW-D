<?php

namespace backend\controllers;

use Yii;
use common\models\discount\CustomerDiscount;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\User;
use common\models\discount\CustomerDiscountLog;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\filters\AccessControl;
use common\components\controllers\BaseController;
/**
 * CustomerDiscountController implements the CRUD actions for CustomerDiscount model.
 */
class CustomerDiscountController extends BaseController
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
                'only' => ['create', 'delete'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
			'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view', 'create', 'delete'],
                        'roles' => ['manageCustomers'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all CustomerDiscount models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => CustomerDiscount::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
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
    public function actionCreate($id)
    {
        $customerDiscountModel = CustomerDiscount::findOne(['customerId' => $id]);
        $userModel = User::findOne(['id' => Yii::$app->user->id]);
        if (empty($customerDiscountModel)) {
            $customerDiscountModel = new CustomerDiscount();
            $customerDiscountModel->customerId = $id;
            //$customerDiscountModel->on(CustomerDiscount::EVENT_CREATE, [new CustomerDiscountLog(), 'create']);
            //$customerDiscountModel->userName = $userModel->publicIdentity;
            $message = 'Discount has been added successfully.';
        } else {
            $message = 'Discount has been updated successfully.';
            // $customerDiscountModel->on(CustomerDiscount::EVENT_EDIT, [new CustomerDiscountLog(), 'edit'], ['oldAttributes' => $customerDiscountModel->getOldAttributes()]);
            // $customerDiscountModel->userName = $userModel->publicIdentity;
        }
        if ($customerDiscountModel->load(Yii::$app->request->post()) && $customerDiscountModel->save()) {
            return [
                'status' => true,
            ];
        } else {
            $errors = ActiveForm::validate($customerDiscountmodel);
            return [
                'status' => false,
                'errors' => current($errors)
            ];
        }
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
        $customerDiscount = CustomerDiscount::findOne(['customerId' => $id]);
        $customerDiscount->delete();
        
        return [
            'status' => true,
        ];
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
        if (($model = CustomerDiscount::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
