<?php

namespace backend\controllers;

use Yii;
use common\models\Qualification;
use backend\models\search\QualificationSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\filters\ContentNegotiator;
use yii\widgets\ActiveForm;
use common\components\controllers\BaseController;
use yii\filters\AccessControl;
use common\models\Lesson;

/**
 * QualificationController implements the CRUD actions for Qualification model.
 */
class QualificationController extends BaseController
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
                'only' => ['update', 'delete', 'create', 'add-group'],
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
                        'actions' => ['index', 'update', 'view', 'delete', 'create', 'add-group'],
                        'roles' => ['manageTeachers'],
                    ],
                ],
            ], 
        ];
    }

    /**
     * Lists all Qualification models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new QualificationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Qualification model.
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
     * Creates a new Qualification model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate($id, $type)
    {
        $model = new Qualification();
        $model->teacher_id = $id;
        $model->type = $type;
        $post = Yii::$app->request->post();
        if ($post) {
            if ($model->load($post)) {
                foreach ($model->programs as $program) {
                    $model->isNewRecord = true;
                    $model->id = null;
                    $model->program_id = $program;
                    $model->save();
                }
                $response = [
                    'status' => true
                ];
            } else {
                $response = [
                    'status' => false,
                    'errors' => ActiveForm::validate($model)
                ];
            }
        } else {
            $data = $this->renderAjax('_form', [
                'model' => $model,
                'teacherId' => $id
            ]);
            $response = [
                'status' => true,
                'data' => $data
            ];
        }
        return $response;
    }
    /**
     * Updates an existing Qualification model.
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
        $post = Yii::$app->request->post();
        if ($post) {
            if ($model->load($post) && $model->save()) {
                $lessons  = Lesson::find()
                    ->notDeleted()
                    ->isConfirmed()
                    ->notCanceled()
                    ->notExpired()
                    ->andWhere(['OR', ['lesson.status' => Lesson::STATUS_UNSCHEDULED], ['AND', ['lesson.status' => [Lesson::STATUS_SCHEDULED, Lesson::STATUS_RESCHEDULED]], ['>', 'lesson.date', (new \DateTime())->format('Y-m-d')]]])
                    ->andWhere(['lesson.teacherId' => $model->teacher_id])
                    ->joinWith(['program' => function($query) use ($model){
                        $query->andWhere(['program.id' => $model->program_id]);
                    }])
                    ->all();
                foreach ($lessons as $lesson) {
                    $lesson->updateAttributes(['teacherRate' => $model->rate]);
                }
                $response = [
                    'status' => true,
                ];
            } else {
                $response = [
                    'status' => false,
                    'errors' => ActiveForm::validate($model)
                ];
            }
        } else {
            $response = [
                'status' => true,
                'data' => $data
            ];
        }
        return $response;
    }

    /**
     * Deletes an existing Qualification model.
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
     * Finds the Qualification model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param string $id
     *
     * @return Qualification the loaded model
     *
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Qualification::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
