<?php

namespace backend\controllers;

use Yii;
use common\models\Item;
use common\models\Invoice;
use yii\data\ActiveDataProvider;
use backend\models\search\ItemSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\filters\ContentNegotiator;
use backend\models\search\InvoiceLineItemSearch;

/**
 * ItemController implements the CRUD actions for Item model.
 */
class ItemController extends \common\components\backend\BackendController
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
                'only' => ['update', 'create', 'filter'],
                'formatParam' => '_format',
                'formats' => [
                   'application/json' => Response::FORMAT_JSON,
                ],
            ],
        ];
    }

    /**
     * Lists all Item models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ItemSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Displays a single Item model.
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
     * Creates a new Item model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model             = new Item();
        $model->locationId = \Yii::$app->session->get('location_id');
        $data              = $this->renderAjax('_form', [
            'model' => $model,
        ]);
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                if (!$model->save(false)) {
                    Yii::erYiiror('Create Item: ' .
                        \yii\helpers\VarDumper::dumpAsString($model->getErrors()));
                } else {
                    return [
                        'status' => true
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

    /**
     * Updates an existing Item model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->canUpdate()) {
            $data = $this->renderAjax('_form', [
                'model' => $model,
            ]);
        } else {
            return [
                'status' => false,
                'message' => 'Lesson and opening balance items cannot be modified from Backend.'
            ];
        }
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                if (!$model->save(false)) {
                    Yii::erYiiror('Create Item: ' .
                        \yii\helpers\VarDumper::dumpAsString($model->getErrors()));
                } else {
                    return [
                        'status' => true
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

    /**
     * Deletes an existing Item model.
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
     * Finds the Item model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Item the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Item::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionPrint()
    {
        $searchModel              = new InvoiceLineItemSearch();
         $currentDate = new \DateTime();
        $searchModel->fromDate = $currentDate->format('M d,Y');
        $searchModel->toDate = $currentDate->format('M d,Y');
        $searchModel->dateRange = $searchModel->fromDate.' - '.$searchModel->toDate;
        $request = Yii::$app->request;
        if ($searchModel->load($request->get())) {
            $invoiceLineItemRequest = $request->get('InvoiceLineItemSearch');
            $searchModel->dateRange = $invoiceLineItemRequest['dateRange'];
        }
        $searchModel->groupByItem = true;
        $dataProvider             = $searchModel->search(Yii::$app->request->queryParams);

        $this->layout             = '/print';

        return $this->render('/report/item/_print', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }
    
    public function actionFilter($invoiceId, $string)
    {
        $invoiceModel = Invoice::findOne($invoiceId);
        $locationId = \Yii::$app->session->get('location_id');
        $itemData = Item::find()
                ->notDeleted()
                ->andWhere(['LIKE', 'item.code', $string])
                ->orWhere(['LIKE', 'item.description', $string])
                ->location($locationId)
                ->active();
        $itemDataProvider = new ActiveDataProvider([
            'query' => $itemData,
        ]);
        $data = $this->renderAjax('/invoice/_form-invoice-line-item', [
            'invoiceModel' => $invoiceModel,
            'itemDataProvider' => $itemDataProvider
        ]);
        return [
            'status' => true,
            'data' => $data
        ];
    }
}
