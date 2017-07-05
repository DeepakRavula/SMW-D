<?php

namespace backend\controllers;

use Yii;
use common\models\CustomerDiscount;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\User;
use common\models\CustomerDiscountLog;
use yii\web\Response;

/**
 * CustomerDiscountController implements the CRUD actions for CustomerDiscount model.
 */
class CustomerDiscountController extends Controller
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
				'only' => ['create'],
				'formats' => [
					'application/json' => Response::FORMAT_JSON,
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
		if(empty($customerDiscountModel)) {
        	$customerDiscountModel = new CustomerDiscount();
			$customerDiscountModel->customerId = $id;
            $customerDiscountModel->on(CustomerDiscount::EVENT_CREATE, [new CustomerDiscountLog(), 'create']);      
        	$customerDiscountModel->userName = $userModel->publicIdentity;
		} else {	
        	$customerDiscountModel->on(CustomerDiscount::EVENT_EDIT, [new CustomerDiscountLog(), 'edit'], ['oldAttributes' => $customerDiscountModel->getOldAttributes()]);
        	$customerDiscountModel->userName = $userModel->publicIdentity;
		}
        if ($customerDiscountModel->load(Yii::$app->request->post()) && $customerDiscountModel->save()) {
			return [
				'status' => true,
				'data' => 'Warning: You have entered a non-approved Arcadia discount. All non-approved discounts must be submitted in writing and approved by Head Office prior to entering a discount, otherwise you are in breach of your agreement.' 
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
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
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
