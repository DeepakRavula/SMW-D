<?php

namespace backend\controllers;

use Yii;
use common\models\CustomerRecurringPaymentEnrolment;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\filters\ContentNegotiator;
use yii\web\Response;
use yii\helpers\Url;
use common\models\Enrolment;
/**
 * BlogController implements the CRUD actions for Blog model.
 */
class CustomerRecurringPaymentEnrolmentController extends \common\components\controllers\BaseController
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
                'only' => ['create','update','delete'],
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
                        'actions' => ['index','create', 'update', 'delete'],
                        'roles' => ['manageBlogs'],
                    ],
					[
                        'allow' => true,
                        'actions' => ['list'],
                        'roles' => ['viewBlogList'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Blog models.
     *
     * @return mixed
     */
   public function actionIndex()
    {
        $blog = Blog::find()
            ->notDeleted()
            ->orderBy(['date' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $blog,
        ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }
    /**
     * Lists all Blog models.
     *
     * @return mixed
     */
    /**
     * Displays a single Blog model.
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
     * Creates a new Blog model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate($id)
    {
        $enrolment = Enrolment::find()
                    ->notDeleted()
                    ->joinWith(['student' => function($query) use($id) {
                            $query->andWhere(['student.customer_id' => $id]);
                        }])
                    ->isConfirmed();
        $enrolmentDataProvider  = new ActiveDataProvider([
            'query' => $enrolment,
            'pagination' => false,
        ]);
        $model = new CustomerRecurringPaymentEnrolment();
        $data = $this->renderAjax('_form', [
            'model' => $model,
            'id' => $id,
            'enrolmentDataProvider' => $enrolmentDataProvider
        ]);
        if ($model->load(Yii::$app->request->post())) {
            $model->customerId = $id;
            $model->expiryDate = (new \DateTime($model->expiryDate))->format('Y-m-d');
            if($model->save()) {
                return [
                    'status' => true
                ];
            }
         else {
            return [
                    'status' => false,
                    'errors' =>$model->getErrors()
                ];
            }
        }
            else {
            return [
                'status' => true,
                'data' => $data
            ];
    }
    }

    /**
     * Updates an existing Blog model.
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
        if (Yii::$app->request->post()) {
            if($model->load(Yii::$app->request->post()) && $model->save()) {
                return [
                    'status' => true
                ];
            } 
        else {
            return [
                    'status' => false,
                    'errors' =>$model->getErrors()
                ];
            }
        }
            else {
            return [
                'status' => true,
                'data' => $data
            ];
        }
    }
    /**
     * Deletes an existing Blog model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param string $id
     *
     * @return mixed
     */
   public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();
            $response = [
                'status' => true,
                'url' => Url::to(['blog/index']),
            ];
        return $response;
    }
	public function actionList()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Blog::find()->notDeleted(),
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
            ]
        ]);

        return $this->render('list', [
            'dataProvider' => $dataProvider,
        ]);
    }
    /**
     * Finds the Blog model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param string $id
     *
     * @return Blog the loaded model
     *
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Blog::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}