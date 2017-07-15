<?php

namespace backend\controllers;

use Yii;
use common\models\PaymentCycle;
use common\models\Enrolment;
use common\models\Lesson;
use common\models\Course;
use yii\data\ActiveDataProvider;
use backend\models\search\EnrolmentSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\base\Model;
use common\models\CourseSchedule;
use yii\helpers\ArrayHelper;
use common\models\Student;
use yii\filters\ContentNegotiator;
use common\models\TeacherAvailability;
use common\models\Program;
use common\models\LocationAvailability;
use yii\helpers\Url;
use common\models\UserLocation;
use common\models\Holiday;
use common\models\User;
use common\models\UserProfile;
use common\models\PhoneNumber;
use common\models\Address;
use common\models\payment\ProformaPaymentFrequency;
use common\models\payment\ProformaPaymentFrequencyLog;
/**
 * EnrolmentController implements the CRUD actions for Enrolment model.
 */
class EnrolmentController extends Controller
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
				'only' => ['add', 'preview', 'delete', 'edit', 'render-day-events','render-resources','schedule', 'update'],
				'formatParam' => '_format',
				'formats' => [
				   'application/json' => Response::FORMAT_JSON,
				],
			],	 
        ];
    }

    /**
     * Lists all Enrolment models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EnrolmentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Enrolment model.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $lessonDataProvider = new ActiveDataProvider([
            'query' => Lesson::find()
				->andWhere([
					'courseId' => $model->course->id,
					'status' => Lesson::STATUS_SCHEDULED
				])
				->notDeleted()
                ->orderBy(['lesson.date' => SORT_ASC]),
            'pagination' => false,
        ]);
        
        $paymentCycleDataProvider = new ActiveDataProvider([
            'query' => PaymentCycle::find()
				->andWhere([
					'enrolmentId' => $id,
				]),
            'pagination' => false,
        ]);
		
        return $this->render('view', [
            'model' => $model,
            'lessonDataProvider' => $lessonDataProvider,
            'paymentCycleDataProvider' => $paymentCycleDataProvider,
        ]);
    }

    public function actionEdit($id)
    {
        $model = $this->findModel($id);
        $oldPaymentFrequency = $model->paymentFrequencyId;
        $lastPaymentFrequency = $model->getPaymentFrequency();
        $model->on(ProformaPaymentFrequency::EVENT_EDIT, [new ProformaPaymentFrequencyLog(), 'edit'], ['lastPaymentFrequency' => $lastPaymentFrequency]);
        $user = User::findOne(['id' => Yii::$app->user->id]);
        $model->userName = $user->publicIdentity;
        if ($model->load(\Yii::$app->getRequest()->getBodyParams(), '') && $model->hasEditable && $model->save()) {
            $model->trigger(ProformaPaymentFrequency::EVENT_EDIT);
            if ((int) $oldPaymentFrequency !== (int) $model->paymentFrequencyId) {
                $model->resetPaymentCycle();
            }
            return ['output' => $model->getPaymentFrequency(), 'message' => ''];
        }
    }
    /**
     * Creates a new Enrolment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Enrolment();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

	public function actionAdd()
    {
		$locationId = Yii::$app->session->get('location_id');
		$request = Yii::$app->request;
		$course = new Course();
		$courseSchedule = new CourseSchedule();
		$user = new User();
		$userProfile = new UserProfile();
		$phoneNumber = new PhoneNumber();
		$address = new Address();
		$userLocation = new UserLocation();
		$student = new Student();
		$course->load(Yii::$app->getRequest()->getBodyParams(), 'Course');
		$user->load(Yii::$app->getRequest()->getBodyParams(), 'User');
		$userProfile->load(Yii::$app->getRequest()->getBodyParams(), 'UserProfile');
		$phoneNumber->load(Yii::$app->getRequest()->getBodyParams(), 'PhoneNumber');
		$address->load(Yii::$app->getRequest()->getBodyParams(), 'Address');
		$student->load(Yii::$app->getRequest()->getBodyParams(), 'Student');
		$courseSchedule->load(Yii::$app->getRequest()->getBodyParams(), 'CourseSchedule');
		
		$user->status = User::STATUS_ACTIVE;
        if($user->save()){
			$userProfile->user_id = $user->id;
			$userProfile->save();
			$userLocation->location_id = $locationId;
			$userLocation->user_id = $user->id;
			$userLocation->save();
			//save address and phone number
			$address->save();
			$user->link('addresses', $address);
			$phoneNumber->user_id = $user->id;
			$phoneNumber->save();
			//save student
			$student->customer_id = $user->id;
			$student->save();
			//save course
			$dayList = Course::getWeekdaysList();
			$course->locationId = $locationId;
			$courseSchedule->day = array_search($courseSchedule->day, $dayList);
			$courseSchedule->studentId = $student->id;
			if($course->save()) {
				$courseSchedule->courseId = $course->id;
				$courseSchedule->save();
				$lesson = new Lesson();
				$conflicts = [];
				$conflictedLessonIds = [];

				$draftLessons = Lesson::find()
					->where(['courseId' => $course->id, 'status' => Lesson::STATUS_DRAFTED])
					->all();
					foreach ($draftLessons as $draftLesson) {
						$draftLesson->setScenario('review');
					}
					Model::validateMultiple($draftLessons);
					foreach ($draftLessons as $draftLesson) {
						if(!empty($draftLesson->getErrors('date'))) {
							$conflictedLessonIds[] = $draftLesson->id;
						}
						$conflicts[$draftLesson->id] = $draftLesson->getErrors('date');
					}
					$holidayConflictedLessonIds = $course->getHolidayLessons();
		$conflictedLessonIds = array_diff($conflictedLessonIds, $holidayConflictedLessonIds);
		$lessonCount = count($draftLessons);
		$conflictedLessonIdsCount = count($conflictedLessonIds);

		$query = Lesson::find()
			->orderBy(['lesson.date' => SORT_ASC])
			->andWhere(['courseId' => $course->id, 'status' => Lesson::STATUS_DRAFTED]);
        $lessonDataProvider = new ActiveDataProvider([
            'query' => $query,
			'pagination' => false,
        ]);
		$data = $this->renderAjax('new/_preview', [
			'courseModel' => $course,
            'lessonDataProvider' => $lessonDataProvider,
            'conflicts' => $conflicts,
			'model' => $lesson,
			'holidayConflictedLessonIds' => $holidayConflictedLessonIds,
			'lessonCount' => $lessonCount,
			'conflictedLessonIdsCount' => $conflictedLessonIdsCount
			]);
		}
			return [
				'status' => true,
				'data' => $data,
			];	
		}
    }

	public function actionConfirm($courseId)
    {
        $courseModel = Course::findOne(['id' => $courseId]);
		$courseModel->updateAttributes([
			'isConfirmed' => true
		]);
        $lessons = Lesson::findAll(['courseId' => $courseModel->id, 'status' => Lesson::STATUS_DRAFTED]);
		foreach ($lessons as $lesson) {
			$lesson->updateAttributes([
				'status' => Lesson::STATUS_SCHEDULED,
			]);
		}
        if (!empty($courseModel->enrolment)) {
            $enrolmentModel = Enrolment::findOne(['id' => $courseModel->enrolment->id]);
            $enrolmentModel->isConfirmed = true;
            $enrolmentModel->save();
            $enrolmentModel->setPaymentCycle();
        }
		Yii::$app->session->setFlash('alert', [
			'options' => ['class' => 'alert-success'],
			'body' => 'Enrolment has been created successfully',
		]);
		return $this->redirect(['student/view', 'id' => $enrolmentModel->student->id]);
	}

	public function actionRenderResources($date, $programId)
    {
        $locationId = Yii::$app->session->get('location_id');
        $date       = \DateTime::createFromFormat('Y-m-d', $date);
		$teachersAvailabilities = TeacherAvailability::find()
				->qualification($locationId, $programId)
				->andWhere(['day' => $date->format('N')])
				->groupBy(['teacher_location_id'])
				->all();
		if (!empty($teachersAvailabilities)) {
			foreach ($teachersAvailabilities as $teachersAvailability) {
				$resources[] = [
					'id'    => $teachersAvailability->teacher->id,
					'title' => $teachersAvailability->teacher->getPublicIdentity(),
				];
			}
		} else {
			$resources[] = [
				'id'    => '0',
				'title' => 'No Teacher Available Today for the Selected Program'
			];
		}
        return $resources;
    }

	public function actionRenderDayEvents($date, $programId)
    {
        $locationId = Yii::$app->session->get('location_id');
        $date       = \DateTime::createFromFormat('Y-m-d', $date);
       	$teachersAvailabilities = TeacherAvailability::find()
			->qualification($locationId, $programId)
			->andWhere(['day' => $date->format('N')])
			->all();
		$events = [];
		foreach ($teachersAvailabilities as $teachersAvailability) {
			$start = \DateTime::createFromFormat('Y-m-d H:i:s', $date->format('Y-m-d') .
				' ' . $teachersAvailability->from_time);
			$end   = \DateTime::createFromFormat('Y-m-d H:i:s', $date->format('Y-m-d') .
				' ' . $teachersAvailability->to_time);
			$events[] = [
				'resourceId' => $teachersAvailability->teacher->id,
				'title'      => '',
				'start'      => $start->format('Y-m-d H:i:s'),
				'end'        => $end->format('Y-m-d H:i:s'),
				'rendering'  => 'background',
			];
		}
		$lessons = $this->getLessons($date, $programId);
		foreach ($lessons as &$lesson) {
			$toTime = new \DateTime($lesson->date);
			$length = explode(':', $lesson->fullDuration);
			$toTime->add(new \DateInterval('PT'.$length[0].'H'.$length[1].'M'));
			$title = $lesson->scheduleTitle;
			$class = $lesson->class;
			$backgroundColor = $lesson->colorCode;
			$events[] = [
				'lessonId' => $lesson->id,
				'resourceId' => $lesson->teacherId,
				'title' => $title,
				'start' => $lesson->date,
				'end' => $toTime->format('Y-m-d H:i:s'),
				'url' => Url::to(['lesson/view', 'id' => $lesson->id]),
				'className' => $class,
				'backgroundColor' => $backgroundColor,
			];
		}
		unset($lesson);
        return $events;
    }
	public function getLessons($date, $programId)
    {
		$locationId = Yii::$app->session->get('location_id');
        $lessons = Lesson::find()
			->location($locationId)
		    ->andWhere(['course.programId' => $programId])
			->andWhere(['lesson.status' => [Lesson::STATUS_SCHEDULED, Lesson::STATUS_COMPLETED]])
			->between($date, $date)
			->notDeleted()
			->all();
        return $lessons;
    }

	public function actionPreview($id)
	{
		$model = $this->findModel($id);
		$data =  $this->renderAjax('/student/enrolment-preview', [
            'model' => $model,
        ]);	
		return [
			'status' => true,
			'data' => $data
		];
	}
    /**
     * Updates an existing Enrolment model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
		$data = $this->renderAjax('/student/enrolment/_form-update', [
			'course' => $model->course,	
			'courseSchedule' => $model->course->courseSchedule,
		]);
		$response = [
			'status' => true,
			'data' => $data,
		];
		$course = $model->course;
		$courseSchedule = $model->courseSchedule;
		$course->load(Yii::$app->getRequest()->getBodyParams(), 'Course');        
		$courseSchedule->load(Yii::$app->getRequest()->getBodyParams(), 'CourseSchedule');        
		if (Yii::$app->request->isPost) {
			$startDate = (new \DateTime($course->startDate))->format('Y-m-d');
			Lesson::deleteAll([
				'courseId' => $model->course->id,
				'status' => Lesson::STATUS_DRAFTED,
			]);
			$lessons		 = Lesson::find()
				->where([
					'courseId' => $model->course->id,
					'status' => Lesson::STATUS_SCHEDULED,
					'type' => Lesson::TYPE_REGULAR
				])
				->joinWith(['reschedule' => function($query) {
                	$query->andWhere(['lesson_reschedule.id' => null]);
				}])
				->andWhere(['>=', 'DATE(date)', $startDate])
				->all();
			$startDate		 = new \DateTime($course->startDate);
			$dayList = Course::getWeekdaysList();
			$courseDay = $dayList[$courseSchedule->day];
			$day = $startDate->format('l');
			if ($day !== $courseDay) {
				$startDate		 = new \DateTime($course->startDate);
				$startDate->modify('next '.$courseDay);
			}
			$teacherId = $course->teacherId;
			$course->generateLessons($lessons, $startDate, $teacherId);
			$rescheduleDate = (new \DateTime($course->startDate))->format('d-m-Y');
			return $this->redirect(['/lesson/review', 'courseId' => $course->id, 'Course[startDate]' => $rescheduleDate]);
		} else {
			return $response;
		}


    }

    /**
     * Deletes an existing Enrolment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param string $id
     *
     * @return mixed
     */
	    /**
     * Deletes an existing Student model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param int $id
     *
     * @return mixed
     */
  
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
		if ($model->course->program->isPrivate()) {
            $lessons = Lesson::find()
                    ->where(['courseId' => $model->courseId])
                    ->andWhere(['>', 'date', (new \DateTime())->format('Y-m-d H:i:s')])
                    ->all();
            foreach ($lessons as $lesson) {
                $lesson->delete();
            }
        }
		$model->delete();
        return [
			'status' => true,
		];
    }

    /**
     * Finds the Enrolment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param string $id
     *
     * @return Enrolment the loaded model
     *
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Enrolment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionSendMail($id)
    {
		$model      = $this->findModel($id);
		$enrolmentRequest = Yii::$app->request->post('Enrolment');
		if($enrolmentRequest) {
			$model->toEmailAddress = $enrolmentRequest['toEmailAddress'];
			$model->subject = $enrolmentRequest['subject'];
			$model->content = $enrolmentRequest['content'];
			if($model->sendEmail())
			{
				Yii::$app->session->setFlash('alert', [
					'options' => ['class' => 'alert-success'],
					'body' => ' Mail has been sent successfully',
				]);
			} else {
				Yii::$app->session->setFlash('alert', [
					'options' => ['class' => 'alert-danger'],
					'body' => 'The customer doesn\'t have email id',
				]);
			}
			return $this->redirect(['view', 'id' => $model->id]);
		}
		
        return $this->redirect(['view', 'id' => $model->id]);
    }
}
