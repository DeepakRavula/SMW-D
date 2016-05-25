<?php

namespace backend\controllers;

use Yii;
use common\models\Enrolment;
use common\models\UserLocation;
use common\models\TeacherAvailability;
use common\models\User;
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
        $model->location_id = Yii::$app->session->get('location_id');

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
		$model = $this->findModel($id);
		$teacherId = $model->teacher_id;
        $model->delete();
        
      //  if (!isset($_GET['ajax'])) {
            return $this->redirect(['user/view','id' => $teacherId]);
    //    }
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
	public function actionAvailableDays() {
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		$session = Yii::$app->session;
		$teacherId = $_POST['depdrop_parents'][0];
		$location_id = $session->get('location_id');
		$teacherLocation = UserLocation::findOne([
			'user_id' => $teacherId,
			'location_id' => $location_id,
		]);
		$availabilities = TeacherAvailability::find()
				->where(['teacher_location_id' => $teacherLocation->id])
				->groupBy(['day'])
				->all();
		$dayList = TeacherAvailability::getWeekdaysList();
		$result = [];
		$output = [];

		foreach($availabilities as $availability) {
			$weekday = $dayList[$availability->day];
			$output[] = [
				'id' => $availability->day,
				'name' => $weekday,
			];
		}
		$result = [
			'output' => $output,	
			'selected' => '',
		];

		return $result;
	}
	
	public function actionAvailableHours() {
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		$request = Yii::$app->request;
		$session = Yii::$app->session;
		$depDrop = $request->post('depdrop_all_params');
		$teacherId = $depDrop['enrolment-teacherId'];
		$day = $depDrop['teacher-availability-day'];
		$location_id = $session->get('location_id');
		$teacherLocation = UserLocation::findOne([
			'user_id' => $teacherId,
			'location_id' => $location_id,
		]);
		$availabilities = TeacherAvailability::find()
				->where([
					'teacher_location_id' => $teacherLocation->id,
					'day' =>  $day,
					])
				->all();
		$result = [];
		$output = [];

		$availableHours = [];

		foreach($availabilities as $availability) {
			$start    = new \DateTime($availability->from_time);
			$end      = new \DateTime($availability->to_time); // add 1 second because last one is not included in the loop
			$interval = new \DateInterval('PT30M');
			$hours   = new \DatePeriod($start, $interval, $end);

			foreach($hours as $hour) {
				$availableHours[] = $hour->format("h:ia");
			}
		}

		foreach($availableHours as $id => $availableHour) {
			$output[] = [
				'id' => (string)$id,
				'name' => $availableHour,
			];
		}
		$result = [
			'output' => $output,	
			'selected' => '',
		];

		return $result;
	}
}
