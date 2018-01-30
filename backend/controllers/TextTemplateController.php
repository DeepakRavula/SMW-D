<?php

namespace backend\controllers;

use Yii;
use common\models\TextTemplate;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\filters\ContentNegotiator;
use yii\web\Response;
use common\components\controllers\BaseController;
use yii\filters\AccessControl;

/**
 * CalendarEventColorController implements the CRUD actions for CalendarEventColor model.
 */
class TextTemplateController extends BaseController
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
                'only' => ['update'],
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
                        'actions' => ['index', 'update'],
                        'roles' => ['manageEmailTemplate'],
                    ],
                ],
            ], 
        ];
    }

    /**
     * Creates a new CalendarEventColor model.
     * If creation is successful, the browser will be redirected to the 'view' page.
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
                'status' => true
            ];
        } else {
            return [
                'status' => true,
                'data' => $data
            ];
        }
    }
    public function actionIndex()
    {
        $textTemplate = TextTemplate::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $textTemplate,
        ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }
    protected function findModel($id)
    {
        if (($model = TextTemplate::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
