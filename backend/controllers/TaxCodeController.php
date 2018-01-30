<?php
namespace backend\controllers;

use Yii;
use common\models\TaxCode;
use backend\models\search\TaxCodeSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\ContentNegotiator;
use yii\web\Response;
use common\components\controllers\BaseController;
use yii\filters\AccessControl;

/**
 * TaxCodeController implements the CRUD actions for TaxCode model.
 */
class TaxCodeController extends BaseController
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
                        'actions' => ['index', 'update', 'view', 'delete', 'create'],
                        'roles' => ['manageTaxes'],
                    ],
                ],
            ], 
        ];
    }

    /**
     * Lists all TaxCode models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TaxCodeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TaxCode model.
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
     * Creates a new TaxCode model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TaxCode();
        $data = $this->renderAjax('_form', [
            'model' => $model,
        ]);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return [
                'status' => true,
            ];
        } else {
            return [
                'status' => true,
                'data' => $data
            ];
        }
    }

    /**
     * Updates an existing TaxCode model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $data = $this->renderAjax('_form', [
            'model' => $model,
        ]);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return [
                'status' => true,
            ];
        }
        return [
            'status' => true,
            'data' => $data
        ];
    }

    /**
     * Deletes an existing TaxCode model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if ($model->delete()) {
            return [
                'status' => true,
            ];
        }
    }

    /**
     * Finds the TaxCode model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param string $id
     *
     * @return TaxCode the loaded model
     *
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TaxCode::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
