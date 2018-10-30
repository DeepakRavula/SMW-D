<?php

namespace backend\controllers;

use Yii;
use common\models\Program;
use common\models\Student;
use common\models\User;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use common\models\Qualification;
use backend\models\search\ProgramSearch;
use yii\filters\AccessControl;
use common\components\controllers\BaseController;
use yii\helpers\Url;
use common\models\Location;
/**
 * ProgramController implements the CRUD actions for Program model.
 */
class ProgramController extends BaseController
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
                'only' => ['update', 'create', 'delete', 'teachers'],
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
                        'roles' => ['managePrograms'],
                    ],
					[
                        'allow' => true,
                        'actions' => ['fetch-rate', 'teachers'],
                        'roles' => ['manageEnrolments'],
                    ],
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
        $request = Yii::$app->request;
        $searchModel         = new ProgramSearch();
        $dataProvider   = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index',
                [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
        ]);
    }

    public function actionFetchRate($id)
    {
        $response         = Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        $program          = Program::findOne(['id' => $id]);

        return $program->rate;
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
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $query = Student::find()
                ->notDeleted()
                ->joinWith(['enrolments' => function ($query) use ($locationId, $id) {
                    $query->location($locationId)
                    ->andWhere(['course.programId' => $id])
                    ->isConfirmed();
                }])
                ->statusActive();

        $studentDataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query = User::find()
            ->joinWith(['userLocation ul' => function ($query) use ($locationId) {
                $query->andWhere(['ul.location_id' => $locationId]);
            }])
            ->joinWith(['qualification' => function ($query) use ($id) {
                $query->andWhere(['program_id' => $id]);
            }])
            ->notDeleted();
        $teacherDataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $this->render(
            'view',
                [
                'model' => $this->findModel($id),
                'studentDataProvider' => $studentDataProvider,
                'teacherDataProvider' => $teacherDataProvider,
        ]
        );
    }

    /**
     * Creates a new Program model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate($type)
    {
        $model = new Program();
        $model->type = $type;
        $data  = $this->renderAjax(
            '_form',
            [
            'model' => $model,
        ]);
        if ($model->load(Yii::$app->request->post())) {
            $model->type=$type;
            $model->save();
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
        $data  = $this->renderAjax(
            '_form',
            [
            'model' => $model,
        ]
        );
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return [
                    'status' => true,
                ];
        } 
        else {
            return [
                'status' => true,
                'data' => $data
            ];
        }
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
        if ($model->deletable()) {
            $model->delete();
            return[
                'status' => true,
                'url' => Url::to(['program/index']),
            ];
        } else {
            return [
                'status' => false,
                'message' => 'Unable to delete. There are courses associated with this program.'
            ];
        }
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

    public function actionTeachers($id, $teacherId)
    {
        $teacherQualification = Qualification::findOne([
                'program_id' => $id,
                'teacher_id' => $teacherId,
                'isDeleted' => false
        ]);
        $locationId = \common\models\Location::findOne(['slug' => \Yii::$app->location])->id;
        $qualifications = Qualification::find()
            ->joinWith(['teacher' => function ($query) use ($locationId) {
                $query->joinWith(['userLocation' => function ($query) use ($locationId) {
                    $query->join('LEFT JOIN', 'user_profile', 'user_profile.user_id = user_location.user_id')
                    ->joinWith('teacherAvailability')
                ->andWhere(['location_id' => $locationId]);
                }]);
            }])
            ->andWhere(['program_id' => $id])
                        ->notDeleted()
            ->orderBy(['user_profile.firstname' => SORT_ASC])
                ->all();
        $result = [];
        $output = [];
        foreach ($qualifications as $i => $qualification) {
            $selectd = false;
            if ($teacherQualification) {
                if ($qualification->teacher->id === $teacherQualification->teacher_id
                    && $teacherQualification) {
                    $selectd = true;
                }
            } elseif ($i === 0) {
                $selectd = true;
            }
            $output[] = [
                'id' => $qualification->teacher->id,
                'text' => $qualification->teacher->publicIdentity,
                'selected' => $selectd
            ];
        }
        if (!$teacherQualification) {
            $selectd = !empty(current($qualifications)->teacher->id) ?
                current($qualifications)->teacher->id : null;
        } else {
            $selectd = $teacherQualification->teacher_id;
        }
        $result = [
            'status' => true,
            'output' => $output,
            'selected' => $selectd,
        ];

        return $result;
    }
}
