<?php
namespace backend\controllers;

use Yii;
use common\models\ClassroomUnavailability;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\filters\AccessControl;
use common\components\controllers\BaseController;
/**
 * ClassroomUnavailabilityController implements the CRUD actions for ClassroomUnavailability model.
 */
class ClassroomUnavailabilityController extends BaseController
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
                'only' => ['create', 'update', 'delete'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
			'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view', 'update', 'create', 'delete'],
                        'roles' => ['manageClassrooms'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all ClassroomUnavailability models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => ClassroomUnavailability::find(),
        ]);

        return $this->render('index', [
                'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ClassroomUnavailability model.
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
     * Creates a new ClassroomUnavailability model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($classroomId)
    {
        $model = new ClassroomUnavailability();
        $model->classroomId = $classroomId;
        $data = $this->renderAjax('/classroom/unavailability/_form', [
            'model' => $model,
        ]);
         
        if ($model->load(Yii::$app->request->post())) {
            if (!empty($model->dateRange)) {
                list($model->fromDate, $model->toDate) = explode(' - ', $model->dateRange);
                $model->fromDate = \DateTime::createFromFormat('M d,Y', $model->fromDate)->format('Y-m-d h:i:s');
                $model->toDate = \DateTime::createFromFormat('M d,Y', $model->toDate)->format('Y-m-d h:i:s');
                if ($model->save()) {
                    return [
                'status' => true
            ];
                } else {
                    return [
                'status' => false,
                'errors' => $model->getErrors($attribute = 'dateRange'),
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
     * Updates an existing ClassroomUnavailability model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $data = $this->renderAjax('//classroom/unavailability/_form', [
            'model' => $model,
        ]);
        if ($model->load(Yii::$app->request->post())) {
            $model->save();
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
     * Deletes an existing ClassroomUnavailability model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
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
     * Finds the ClassroomUnavailability model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return ClassroomUnavailability the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ClassroomUnavailability::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
