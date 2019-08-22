<?php

namespace backend\controllers;

use Yii;
use common\models\Holiday;
use backend\models\search\HolidaySearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\ContentNegotiator;
use yii\web\Response;
use common\models\TeacherUnavailability;
use common\models\User;
use common\components\controllers\BaseController;
use yii\filters\AccessControl;
use yii\widgets\ActiveForm;

/**
 * HolidayController implements the CRUD actions for Holiday model.
 */
class TeacherUnavailabilityController extends BaseController
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
                'only' => ['update', 'create', 'delete', 'validate'],
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
                        'actions' => ['index', 'update', 'view', 'delete', 'create', 'validate'],
                        'roles' => ['manageTeachers'],
                    ],
                ],
            ], 
        ];
    }

    /**
     * Lists all Holiday models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new HolidaySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Holiday model.
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
     * Creates a new Holiday model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate($id)
    {
        $model = new TeacherUnavailability();
        $teacher = User::findOne(['id' => $id]);
        $data  = $this->renderAjax('_form', [
            'model' => $model,
            'teacher' => $teacher
        ]);
        $model->teacherId = $teacher->id;
        if ($model->load(Yii::$app->request->post())) {
            $model->fromDateTime = (new \DateTime($model->fromDateTime))->format('Y-m-d H:i:s');
            $model->toDateTime = (new \DateTime($model->toDateTime))->format('Y-m-d H:i:s');
            if ($model->save()) {
                return [
                    'status' => true
                ];
            } else {
                return [
                    'status' => false,
                    'errors' => $model->getErrors(),
                ];
            }
        } else {
            return [
                'status' => true,
                'data' => $data
            ];
        }
    }

    public function actionValidate($id = null) {
        $model = new TeacherUnavailability();
        if(!empty($id)) {
            $model = TeacherUnavailability::findOne(['id' => $id]);
        } 
        $request = Yii::$app->request;
        if ($model->load($request->post())) {
            return  ActiveForm::validate($model);
        }
    }

    /**
     * Updates an existing Holiday model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
	    $teacher = User::findOne(['id' => $id]);
        $model->fromDateTime = (new \DateTime($model->fromDateTime))->format('M d, Y H:i');
        $model->toDateTime = (new \DateTime($model->toDateTime))->format('M d, Y H:i');
        $data = $this->renderAjax('_form', [
            'model' => $model,
	        'teacher' => $teacher
        ]);
        if ($model->load(Yii::$app->request->post())) {
            $model->fromDateTime = (new \DateTime($model->fromDateTime))->format('Y-m-d H:i:s');
            $model->toDateTime = (new \DateTime($model->toDateTime))->format('Y-m-d H:i:s');
            if($model->save()) {
                return [
                    'status' => true
                ];
            } else {
                return [
                    'status' => false,
                    'errors' => $model->getErrors(),
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
     * Deletes an existing Holiday model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return [
            'status' => true,
        ];
    }

    /**
     * Finds the Holiday model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param string $id
     *
     * @return Holiday the loaded model
     *
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TeacherUnavailability::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
