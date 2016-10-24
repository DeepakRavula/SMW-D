<?php

namespace backend\controllers;

use Yii;
use common\models\User;
use common\models\UserLocation;
use common\models\TeacherAvailability;
use backend\models\TeacherAvailabilitySearch;
use yii\web\Controller;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use common\models\Lesson;
use common\models\Program;
use common\models\Invoice;
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
		$teacherId = $model->teacher->id;
        $model->delete();
     	Yii::$app->session->setFlash('alert', [
       	    'options' => ['class' => 'alert-success'],
          	'body' => 'Teacher Availability has been deleted successfully'
        ]);
		$roles = ArrayHelper::getColumn(
            	Yii::$app->authManager->getRolesByUser($teacherId),
            'name'
        );
			$role = end($roles);
            return $this->redirect(['user/view', 'UserSearch[role_name]' => $role, 'id' => $teacherId]);
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
		if(! empty($teacherLocation)){
			$availabilities = TeacherAvailability::find()
				->where(['teacher_location_id' => $teacherLocation->id])
				->groupBy(['day'])
				->all();
		}
		$dayList = TeacherAvailability::getWeekdaysList();
		$result = [];
		$output = [];
		
		if(! empty($availabilities)){
			foreach($availabilities as $availability) {
				$weekday = $dayList[$availability->day];
				$output[] = [
					'id' => $availability->day,
					'name' => $weekday,
				];
			}
		}
		$result = [
			'output' => $output,	
			'selected' => '',
		];

		return $result;
	}

	public function actionAvailabilityWithEvents($id) {
		$session = Yii::$app->session;
        $response = Yii::$app->response;
		$response->format = Response::FORMAT_JSON;
		$locationId = $session->get('location_id');
		$teacherAvailabilities = TeacherAvailability::find()
		->joinWith(['userLocation' => function($query) use($id) {
			$query->joinWith(['userProfile' => function($query) use($id){
				$query->where(['user_profile.user_id' => $id]);
			}]);
		}])
		->all();
		$availableHours = [];
		foreach($teacherAvailabilities as $teacherAvailability) {
			$availableHours[] = [
				'start' => $teacherAvailability->from_time,
				'end' => $teacherAvailability->to_time,
				'dow' => [$teacherAvailability->day],
				'className' => 'teacher-available',
				'rendering' => 'inverse-background'
			];
		}

		$lessons = [];
		$lessons = Lesson::find()
			->joinWith(['course' => function($query) {
				$query->andWhere(['locationId' => Yii::$app->session->get('location_id')]);
			}])
			->where(['lesson.teacherId' => $id])
			->andWhere(['NOT', ['lesson.status' => [Lesson::STATUS_CANCELED, Lesson::STATUS_DRAFTED]]])
			->all();
	   $events = [];
		foreach ($lessons as &$lesson) {
			$toTime = new \DateTime($lesson->date);
			$length = explode(':', $lesson->duration);
			$toTime->add(new \DateInterval('PT' . $length[0] . 'H' . $length[1] . 'M'));
			if ((int) $lesson->course->program->type === (int) Program::TYPE_GROUP_PROGRAM) {
                $title = $lesson->course->program->name . ' ( ' . $lesson->course->getEnrolmentsCount() . ' ) ';
            } else {
            	$title = $lesson->enrolment->student->fullName . ' ( ' .$lesson->course->program->name . ' ) ';
			}
            $class = null;
			if (! empty($lesson->proFormaInvoice)) {
                if (in_array($lesson->proFormaInvoice->status, [Invoice::STATUS_PAID, Invoice::STATUS_CREDIT])) {
                    $class = 'proforma-paid';
                } else {
                    $class = 'proforma-unpaid';
                }
            }  
			$events[]= [
				'start' => $lesson->date,
				'end' => $toTime->format('Y-m-d H:i:s'),
                'className' => $class,
                'title' => $title,
			];
		}
		unset($lesson);

		return [
			'availableHours' => $availableHours,
			'events' => $events,
		];
	}
}
