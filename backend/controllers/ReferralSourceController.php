<?php

namespace backend\controllers;

use common\components\controllers\BaseController;
use common\models\ReferralSource;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * ReferralSourceController implements the CRUD actions for ReferralSource model.
 */
class ReferralSourceController extends BaseController
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
                'only' => ['create', 'update'],
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
                        'roles' => ['manageReleaseNotes'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all ReleaseNotes models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $query = ReferralSource::find()
            ->notDeleted();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ReferralSource model.
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
     * Creates a new ReferralSource model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ReferralSource();
        $data = $this->renderAjax('_form', [
            'model' => $model,
        ]);
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
            return [
                'status' => true,
            ];
            }  else {
                return [
                    'status' => false,
                    'errors' => Activeform::validate($model),
                ];

            }
        } else {
            return [
                'status' => true,
                'data' => $data,
            ];
        }
    }

    /**
     * Updates an existing ReferralSource model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {

        $model = $this->findModel($id);
        if ($model->isOther()) {
            return [
                'status' => false,
                'message' => 'This referral source cannot be modified from Backend.'
            ];
           
        } else {
            $data = $this->renderAjax('_form', [
                'model' => $model,
            ]);
        }
        if ($model->load(Yii::$app->request->post())) { 
            if ($model->save()) {
                return [
                    'status' => true,
                ];
            } else {
                return [
                    'status' => false,
                    'errors' => ActiveForm::validate($model),
                ];
            }
        } else {
            return [
                'status' => true,
                'data' => $data,
            ];
        }
    }

    /**
     * Deletes an existing ReferralSource model.
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
     * Finds the ReferralSource model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param string $id
     *
     * @return ReferralSource the loaded model
     *
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ReferralSource::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
