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
use yii\filters\ContentNegotiator;

/**
 * ItemCategoryController implements the CRUD actions for ItemCategory model.
 */
class ItemCategoryController extends Controller
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
                'only' => ['items', 'get-item-values', 'create', 'update'],
                'formatParam' => '_format',
                'formats' => [
                   'application/json' => Response::FORMAT_JSON,
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
            'query' => ItemCategory::find(),
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
        $locationId   = Yii::$app->session->get('location_id');
        $dataProvider = new ActiveDataProvider([
            'query'   => Item::find()
                            ->where(['itemCategoryId' => $id])
                            ->location($locationId),
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
                $model->save(false);
                return [
                    'status' => true
                ];
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
        $data = $this->renderAjax('_form', [
            'model' => $model,
        ]);
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                $model->save(false);
                return [
                    'status' => true
                ];
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

        return $this->redirect(['index']);
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
        $session        = Yii::$app->session;
        $locationId     = $session->get('location_id');
        $itemCategoryId = $_POST['depdrop_parents'][0];
        $items          = Item::find()
                            ->where(['itemCategoryId' => $itemCategoryId])
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

        return [
            'description' => $item->description,
            'price' => $item->price,
            'code' => $item->code,
            'royaltyFree' => $item->royaltyFree,
            'tax' => $item->taxStatusId
        ];
    }
}