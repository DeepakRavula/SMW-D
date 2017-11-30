<?php

namespace backend\controllers;

use Yii;
use common\models\TaxStatus;
use yii\web\Response;
use yii\filters\ContentNegotiator;
use backend\models\LineItemMultiTax;
use common\models\InvoiceLineItem;
use backend\models\search\TaxStatusSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TaxStatusController implements the CRUD actions for TaxStatus model.
 */
class TaxStatusController extends Controller
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
                'only' => ['edit-line-item-tax'],
                'formatParam' => '_format',
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
        ];
    }

    /**
     * Lists all TaxStatus models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TaxStatusSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TaxStatus model.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new TaxStatus model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TaxStatus();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing TaxStatus model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param string $id
     *
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
     * Deletes an existing TaxStatus model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the TaxStatus model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param string $id
     *
     * @return TaxStatus the loaded model
     *
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TaxStatus::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    public function actionEditLineItemTax()
    {
        $lineItemIds = Yii::$app->request->get('InvoiceLineItem')['ids'];
        $multiLineItemTax = new LineItemMultiTax(); 
        $lineItem = $multiLineItemTax->setModel($lineItemIds);
        $data = $this->renderAjax('/invoice/line-item/_form-tax', [
            'lineItemIds' => $lineItemIds,
            'model' => $lineItem
        ]);
        $post = Yii::$app->request->post();
        if ($post) {
            foreach ($lineItemIds as $lineItemId) {
                $lineItem = InvoiceLineItem::findOne($lineItemId);
                $lineItem->load($post);
                if (!$lineItem->save()) {
                    print_r($lineItem->getErrors());die;
                }
            }
            return [
                'status' => true,
                'message' => 'Tax successfully updated!'
            ];
        } else {
            return [
                'status' => true,
                'data' => $data
            ];
        }
    }
}
