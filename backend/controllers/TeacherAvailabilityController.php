<?php

namespace backend\controllers;

use Yii;
use common\models\Enrolment;
use common\models\TeacherAvailability;
use backend\models\TeacherAvailabilitySearch;
use yii\web\Controller;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TeacherAvailabilityController implements the CRUD actions for TeacherAvailability model.
 */
class TeacherAvailabilityController extends Controller
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
        ];
    }

    /**
     * Lists all TeacherAvailability models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TeacherAvailabilitySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TeacherAvailability model.
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
     * Creates a new TeacherAvailability model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TeacherAvailability();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing TeacherAvailability model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing TeacherAvailability model.
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
     * Finds the TeacherAvailability model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return TeacherAvailability the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TeacherAvailability::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
	public function actionDays() {
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		$teacherId = $_POST['depdrop_parents'][0];
		$days = ArrayHelper::map(
			TeacherAvailability::find()
				->where(['teacher_id' => $teacherId])
				->all(),
			'id', 'day'
		);
		$dayList = TeacherAvailability::getWeekdaysList();
		$result = [];
		$output = [];

		foreach($days as $id=> $day) {
			$weekday = $dayList[$day];
			$output[] = [
				'id' => $day,
				'name' => $weekday,
			];
		}
		$result = [
			'output' => $output,	
			'selected' => '',
		];

		return $result;
	}
	
	public function actionFromtimes() {
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		$teacherId = $_POST['depdrop_parents'][0];
		$fromTimes = ArrayHelper::map(
			TeacherAvailability::find()
				->where(['teacher_id' => $teacherId])
				->all(),
			'id', 'from_time'
		);
		$result = [];
		$output = [];

		foreach($fromTimes as $id=> $fromTime) {
			$output[] = [
				'id' => $fromTime,
				'name' => $fromTime,
			];
		}
		$result = [
			'output' => $output,	
			'selected' => '',
		];

		return $result;
	}
}
