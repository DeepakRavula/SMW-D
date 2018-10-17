<?php

namespace backend\controllers;

use Yii;
use common\models\ItemCategory;
use common\models\Item;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use common\models\TaxStatus;
use yii\filters\ContentNegotiator;
use backend\models\search\InvoiceLineItemSearch;
use common\components\controllers\BaseController;
use yii\filters\AccessControl;
use common\models\InvoiceLineItem;
use common\models\Location;
/**
 * ItemCategoryController implements the CRUD actions for ItemCategory model.
 */
class ItemCategoryController extends BaseController
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
                'only' => ['items', 'get-item-values', 'create', 'update','delete'],
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
                        'actions' => ['index', 'update', 'view', 'delete', 'create', 'items', 
                            'get-item-values', 'print','invoice-number'],
                        'roles' => ['manageItemCategory'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['print'],
                        'roles' => ['owner'],
                    ],
                ],
            ],  
        ];
    }

    /**
     * Lists all ItemCategory models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => ItemCategory::find()
                        ->notDeleted(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ItemCategory model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        $locationId   = \common\models\Location::findOne(['slug' => \Yii::$app->location])->id;
        $dataProvider = new ActiveDataProvider([
            'query'   => Item::find()
                            ->andWhere(['itemCategoryId' => $id])
                            ->location($locationId)
                            ->notDeleted()
        ]);

        return $this->render('view', [
            'model' => $this->findModel($id),
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new ItemCategory model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ItemCategory();
        $data  = $this->renderAjax('_form', [
            'model' => $model,
        ]);
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                if (!$model->save(false)) {
                    Yii::erYiiror('Create Item Category: ' .
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
     * Updates an existing ItemCategory model.
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
                    Yii::erYiiror('Create Item Category: ' .
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
     * Deletes an existing ItemCategory model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

         return [
		    'status' => true
		];
	}

    /**
     * Finds the ItemCategory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return ItemCategory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ItemCategory::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionItems()
    {
        $locationId     = Location::findOne(['slug' => \Yii::$app->location])->id;
        $itemCategoryId = $_POST['depdrop_parents'][0];
        $items          = Item::find()
                            ->notDeleted()
                            ->andWhere(['itemCategoryId' => $itemCategoryId])
                            ->location($locationId)
                            ->active()
                            ->all();
        $result = [];
        $output = [];
        foreach ($items as  $item) {
            $output[] = [
                'id' => $item->id,
                'name' => $item->code,
            ];
        }
        $result = [
            'output' => $output,
            'selected' => '',
        ];

        return $result;
    }

    public function actionGetItemValues($itemId)
    {
        $item = Item::findOne($itemId);
        $taxStatus = TaxStatus::findOne(['id' => $item->taxStatusId]);
        $taxPercentage = $taxStatus->taxTypeTaxStatusAssoc->taxType->taxCode->rate;
        return [
            'description' => $item->description,
            'price' => $item->price,
            'code' => $item->code,
            'royaltyFree' => $item->royaltyFree,
            'tax' => $item->taxStatusId,
            'taxPercentage' => $taxPercentage,
            'total' => $item->price * ($taxPercentage / 100)
        ];
    }

    public function actionPrint()
    {
        $searchModel                      = new InvoiceLineItemSearch();
        $currentDate = new \DateTime();
        $searchModel->fromDate = $currentDate->format('M d,Y');
        $searchModel->toDate = $currentDate->format('M d,Y');
        $searchModel->dateRange = $searchModel->fromDate . ' - ' . $searchModel->toDate;
        $request = Yii::$app->request;
        if ($searchModel->load($request->get())) {
            $invoiceLineItemRequest = $request->get('InvoiceLineItemSearch');
	    $searchModel->groupByMethod = $invoiceLineItemRequest['groupByMethod'];
            $searchModel->dateRange = $invoiceLineItemRequest['dateRange'];
        }
        $searchModel->groupByItemCategory = true;
        $dataProvider                     = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination=false;
        $this->layout = '/print';

        return $this->render(
            '/report/item-category/_print',
                [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
        ]
        );
    }
    public function actionInvoiceNumber($lineItemId)
    {
       $invoiceLineItem= InvoiceLineItem::findOne(['id' => $lineItemId]);
       $invoiceId=$invoiceLineItem->invoice->id;
       return $invoiceId;
    }
}
