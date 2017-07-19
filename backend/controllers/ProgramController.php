<?php

namespace backend\controllers;

use Yii;
use common\models\Program;
use common\models\Student;
use common\models\User;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use backend\models\search\ProgramSearch;

/**
 * ProgramController implements the CRUD actions for Program model.
 */
class ProgramController extends Controller
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
				'only' => ['update'],
				'formats' => [
					'application/json' => Response::FORMAT_JSON,
				],
        	],
        ];
    }

    /**
     * Lists all Program models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProgramSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $model = new Program();
        $model->type = $searchModel->type;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('alert', [
                'options' => ['class' => 'alert-success'],
                'body' => 'Program has been created successfully',
        ]);

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('index', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Program model.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionView($id)
    {
        $locationId = Yii::$app->session->get('location_id');
        $query = Student::find()
                ->notDeleted()
                ->joinWith(['enrolment' => function ($query) use ($locationId, $id) {
                    $query->location($locationId)
                        ->where(['course.programId' => $id])
						->isConfirmed();
                }])
				->active();

        $studentDataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query = User::find()
                ->joinWith(['userLocation ul' => function ($query) use ($locationId) {
                    $query->where(['ul.location_id' => $locationId]);
                }])
                ->joinWith('qualification')
                ->where(['program_id' => $id])
                ->notDeleted();
        $teacherDataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $this->render('view', [
            'model' => $this->findModel($id),
            'studentDataProvider' => $studentDataProvider,
            'teacherDataProvider' => $teacherDataProvider,
        ]);
    }

    /**
     * Creates a new Program model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Program();
        $model->status = Program::STATUS_ACTIVE;
        $request = Yii::$app->request;
        $programRequest = $request->get('ProgramSearch');
        $model->type = $programRequest['type'];
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('alert', [
                'options' => ['class' => 'alert-success'],
                'body' => 'Program has been created successfully',
        ]);

            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Program model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $data = $this->renderAjax('//program/_form', [
            'model' => $model,
        ]);
         if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if ($model->status == Program::STATUS_INACTIVE) {
           return $this->redirect(['index', 'ProgramSearch[type]' => $model->type]);
            } else {
                 return  [
				'status' => true,
			];
                
            }
        }
        return [
            'status' => true,
            'data' => $data
        ];
    }

    /**
     * Deletes an existing Program model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();
        Yii::$app->session->setFlash('alert', [
               'options' => ['class' => 'alert-success'],
               'body' => 'Program has been deleted successfully',
        ]);

        return $this->redirect(['index', 'ProgramSearch[type]' => $model->type]);
    }

    /**
     * Finds the Program model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param int $id
     *
     * @return Program the loaded model
     *
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Program::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
