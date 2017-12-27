<?php

namespace backend\controllers;

use Yii;
use common\models\Lesson;
use common\models\PrivateLesson;
use common\models\Enrolment;
use common\models\Course;
use common\models\LessonReschedule;
use yii\data\ActiveDataProvider;
use backend\models\search\LessonSearch;
use yii\base\Model;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\Note;
use common\models\Student;
use common\models\CourseSchedule;
use yii\web\Response;
use common\models\Payment;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use common\models\timelineEvent\TimelineEventEnrolment;
use common\models\LessonLog;
use yii\base\ErrorException;
use common\models\User;
use common\models\timelineEvent\TimelineEventLesson;
use yii\filters\ContentNegotiator;
use common\models\PaymentCycle;
use common\models\Invoice;
use common\models\InvoiceLog;
use common\models\lesson\BulkReschedule;
use common\models\lesson\BulkRescheduleLesson;

/**
 * LessonController implements the CRUD actions for Lesson model.
 */
class LessonController extends \common\components\controllers\BaseController
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
                'only' => ['modify-classroom', 'merge', 'update-field',
                    'validate-on-update', 'modify-lesson', 'edit-classroom', 
                    'payment', 'substitute','update'],
                'formatParam' => '_format',
                'formats' => [
                   'application/json' => Response::FORMAT_JSON,
                ],
            ],
        ];
    }
		
    /**
     * Lists all Lesson models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new LessonSearch();
        $request = Yii::$app->request;
        $lessonRequest = $request->get('LessonSearch');
        $searchModel->lessonStatus = Lesson::STATUS_SCHEDULED;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        if (!empty($lessonRequest['dateRange'])) {
            $searchModel->dateRange = $lessonRequest['dateRange'];
        }
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    /**
     * Displays a single Lesson model.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function actionView($id)
    {
        $locationId = \common\models\Location::findOne(['slug' => \Yii::$app->location])->id;
        $model = $this->findModel($id);
        $model->duration = $model->fullDuration;
        $notes = Note::find()
                ->andWhere(['instanceId' => $model->id, 'instanceType' => Note::INSTANCE_TYPE_LESSON])
                ->orderBy(['createdOn' => SORT_DESC]);

        $noteDataProvider = new ActiveDataProvider([
            'query' => $notes,
        ]);

		$groupLessonStudents = Student::find()
                        ->notDeleted()
			->joinWith(['enrolment' => function($query) use($id) {
				$query->joinWith(['course' => function($query) use($id) {
					$query->joinWith(['program' => function($query) use($id) {
						$query->group();
					}]);
					$query->joinWith(['lessons' => function($query) use($id) {
						$query->andWhere(['lesson.id' => $id]);
					}]);
				}])
				->notDeleted()
		        ->isConfirmed();
			}])
			->location($locationId);

        $studentDataProvider = new ActiveDataProvider([
            'query' => $groupLessonStudents,
        ]);
        $payments = Payment::find()
			->joinWith(['lessonCredit' => function($query) use($id){
				$query->andWhere(['lesson_payment.lessonId' => $id]);	
			}]);
        $paymentsDataProvider = new ActiveDataProvider([
            'query' => $payments,
        ]);
        return $this->render('view', [
            'model' => $model,
            'noteDataProvider' => $noteDataProvider,
            'studentDataProvider' => $studentDataProvider,
            'paymentsDataProvider' => $paymentsDataProvider
        ]);
    }

    /**
     * Creates a new Lesson model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate($studentId)
    {
        $response = \Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        $model = new Lesson();
        $model->locationId = \common\models\Location::findOne(['slug' => \Yii::$app->location])->id;
        $model->setScenario(Lesson::SCENARIO_CREATE);
        $request = Yii::$app->request;
        $studentModel = Student::findOne($studentId);
        $model->programId = !empty($studentModel->firstPrivateCourse) ? $studentModel->firstPrivateCourse->programId : null;
        $model->teacherId = !empty($studentModel->firstPrivateCourse) ? $studentModel->firstPrivateCourse->teacherId : null;
        $data = $this->renderAjax('/student/_form-lesson', [
            'model' => $model,
            'studentModel' => $studentModel
        ]);
        if ($model->load($request->post())) {
            $studentEnrolment = Enrolment::find()
                ->notDeleted()
                ->isConfirmed()
                ->joinWith(['course' => function($query) use($model){
                    $query->where(['course.programId' => $model->programId]);
                }])
                ->where(['studentId' => $studentId])
                ->one();
            if ($studentEnrolment) {
                $model->courseId = $studentEnrolment->courseId;
            } else {
                $course                   = $model->createExtraLessonCourse();
                $course->studentId        = $studentId;
                $course->createExtraLessonEnrolment();
                $courseSchedule           = new CourseSchedule();
                $courseSchedule->courseId = $course->id;
                $courseSchedule->day      = (new \DateTime($model->date))->format('N');
                $courseSchedule->duration = (new \DateTime($model->duration))->format('H:i:s');
                $courseSchedule->fromTime = (new \DateTime($model->date))->format('H:i:s');
                if (!$courseSchedule->save()){
                    Yii::error('Course Schedule: ' . \yii\helpers\VarDumper::dumpAsString($courseSchedule->getErrors()));
                }
                $model->courseId          = $course->id;
            }
            $model->status = Lesson::STATUS_SCHEDULED;
            $model->isConfirmed = true;
            $model->isDeleted = false;
            $model->type = Lesson::TYPE_EXTRA;
            $lessonDate = \DateTime::createFromFormat('Y-m-d g:i A', $model->date);
            $model->date = $lessonDate->format('Y-m-d H:i:s');
            if ($model->save()) {
                $response = [
                    'status' => true,
                    'url' => Url::to(['lesson/view', 'id' => $model->id])
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

    public function actionValidateOnUpdate($id, $teacherId = null)
    {
        $errors = [];
        $model = $this->findModel($id);
        if(empty($teacherId)) {
            $model->setScenario(Lesson::SCENARIO_EDIT);
        }
        if ($model->load(Yii::$app->request->post())) {
            if (!empty($model->date)) {
                $errors = ActiveForm::validate($model);
                return $errors;
            } else {
                return $errors;
            }
        }
    }

    public function actionValidate($studentId)
    {
		$response = \Yii::$app->response;
		$response->format = Response::FORMAT_JSON;
        $model = new Lesson();
        $model->type = Lesson::TYPE_EXTRA;
		$model->setScenario(Lesson::SCENARIO_CREATE);
		$request = Yii::$app->request;
        if ($model->load($request->post())) {
			$studentEnrolment = Enrolment::find()
			   ->joinWith(['course' => function($query) use($model){
				   $query->where(['course.programId' => $model->programId]);
			   }])
				->where(['studentId' => $studentId])
				->one();
            $model->courseId = !empty($studentEnrolment) ? $studentEnrolment->courseId : null;
            $model->studentId = $studentId;
			return  ActiveForm::validate($model);
		}
    }

	public function actionFetchDuration($id)
	{
        $model = $this->findModel($id);
		return $model->duration;	
	}
    /**
     * Updates an existing Lesson model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param string $id
     *
     * @return mixed
     */
	public function actionEditClassroom($id)
	{
		$request = Yii::$app->request;
        $model = $this->findModel($id);
		$model->setScenario(Lesson::SCENARIO_EDIT);
		$model->on(Lesson::EVENT_RESCHEDULE_ATTEMPTED,
            [new LessonReschedule(), 'reschedule'], ['oldAttrtibutes' => $model->getOldAttributes()]);
	 $model->on(Lesson::EVENT_RESCHEDULED,
       [new LessonLog(), 'reschedule'], ['oldAttrtibutes' => $model->getOldAttributes()]);	
		if ($model->load($request->post())) {
			if($model->save()) {
				return [
					'status' => true
				];
			} else {
				return [
					'status' => false,
					'errors' => ActiveForm::validate($model)
				];
			}
		}		
	}
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $oldDate = $model->date;
        $response = \Yii::$app->response;
		$response->format = Response::FORMAT_JSON;
        $model->date =Yii::$app->formatter->asDateTime($model->date);
        $oldTeacherId = $model->teacherId;
        $user = User::findOne(['id'=>Yii::$app->user->id]);
        $model->userName = $user->publicIdentity;
        $model->on(Lesson::EVENT_RESCHEDULE_ATTEMPTED,
                [new LessonReschedule(), 'reschedule'], ['oldAttrtibutes' => $model->getOldAttributes()]);
        $model->on(Lesson::EVENT_RESCHEDULED,
                [new LessonLog(), 'reschedule'], ['oldAttrtibutes' => $model->getOldAttributes()]);
        $lessonDate = \DateTime::createFromFormat('Y-m-d H:i:s', $model->date);
		
		$request = Yii::$app->request;
		$userModel = $request->post('User');
		if ($model->hasExpiryDate()) {
			$privateLessonModel = PrivateLesson::findOne(['lessonId' => $model->id]);
			$privateLessonModel->load(Yii::$app->getRequest()->getBodyParams(), 'PrivateLesson');
			$privateLessonModel->expiryDate = (new \DateTime($privateLessonModel->expiryDate))->format('Y-m-d H:i:s');
			$privateLessonModel->save();
		} 	
        if ($model->load($request->post()) || !empty($userModel)) {
			if(empty($model->date)) {
				$model->date =  $oldDate;
				$model->status = Lesson::STATUS_UNSCHEDULED;
				$model->save();
				  $response = [
                    'status' => true,
                    'url' => Url::to(['lesson/view', 'id' => $model->id])
                ];
            } else {
				if(!empty($userModel)) {
					$model->date = $userModel['fromDate'];
					$model->duration = (new \DateTime($model->duration))->format('H:i');
				}
				if (new \DateTime($oldDate) != new \DateTime($model->date) || $oldTeacherId != $model->teacherId) {
					$model->setScenario(Lesson::SCENARIO_EDIT);
					$validate = $model->validate();
				}
				$lessonConflict = $model->getErrors('date');
				$message = current($lessonConflict);
				if(! empty($lessonConflict)){
					 $response = [
                        'status' => false,
                        'errors' => $message
                    ];
                } else {
					if($model->course->program->isPrivate()) {
						$duration = new \DateTime($model->duration);
						$model->duration = $duration->format('H:i:s');
					}
					$lessonDate = \DateTime::createFromFormat('d-m-Y g:i A', $model->date);
					$model->date = $lessonDate->format('Y-m-d H:i:s');
                    if(! $model->save()) {
					   $response = [
                            'status' => false,
                            'errors' => $model->getErrors()
                        ];
                    }
                  $response = [
                        'status' => true,
                        'url' => Url::to(['lesson/view', 'id' => $model->id])
                    ];
                }
			}
            return $response;
        }
        return $this->render('_form-private-lesson', [
			'model' => $model,
			'privateLessonModel' => !empty($privateLessonModel) ? $privateLessonModel : null
		]);
    }

    /**
     * Finds the Lesson model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param string $id
     *
     * @return Lesson the loaded model
     *
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $session = Yii::$app->session;
        $locationId = \common\models\Location::findOne(['slug' => \Yii::$app->location])->id;
        $model = Lesson::find()->location($locationId)
            ->where(['lesson.id' => $id, 'isDeleted' => false])->one();
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

	public function getConflicts($course)
	{
		$conflicts = [];
		$conflictedLessonIds = [];
		$draftLessons = Lesson::find()
			->where(['courseId' => $course->id, 'isConfirmed' => false,
				'status' => Lesson::STATUS_SCHEDULED])
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
		return [
			'conflicts' => $conflicts,
			'lessonIds' => $conflictedLessonIds
		];
	}
	public function fetchConflictedLesson($course)
	{

		$conflictedLessons = $this->getConflicts($course);
		$lessons = Lesson::find()
			->where(['courseId' => $course->id, 'isConfirmed' => false,
				'status' => Lesson::STATUS_SCHEDULED])
			->all();
		if(!empty($conflictedLessons['lessonIds'])) {
			$lessons = Lesson::find()
				->orderBy(['lesson.date' => SORT_ASC])
				->andWhere(['IN', 'lesson.id', $conflictedLessons['lessonIds']])
				->all();	
		}
		return $lessons;
	}
	public function resolveSingleLesson($lesson, $oldDate)
	{
		if(! empty($lesson->date)) {
			$lesson->setScenario(Lesson::SCENARIO_EDIT_REVIEW_LESSON);
			$lesson->date = (new \DateTime($lesson->date))->format('Y-m-d H:i:s');
		} else {
			$lesson->date = $oldDate;
			$lesson->status = Lesson::STATUS_UNSCHEDULED;
		}
		if($lesson->save()) {
			$response = [
				'status' => true
			];
		} else {
			$response = [
				'status' => false,
				'errors' => ActiveForm::validate($lesson),
			];
		}	
		return $response;
	}
	public function resolveAllLesson($conflictedLessons, $lesson)
	{
		foreach($conflictedLessons as $conflictedLesson) {
			$conflictedLesson->duration = $lesson->duration;
			if(! empty($lesson->date)) {
				$day = (new \DateTime($lesson->date))->format('N');
				$lessonDay = (new \DateTime($conflictedLesson->date))->format('N');
				$time = (new \DateTime($lesson->date))->format('H:i:s'); 
				list($hour, $minute, $second) = explode(':', $time);
				$dayList = Course::getWeekdaysList();
				$dayName = $dayList[$day];
				if($day === $lessonDay) {
					$lessonDate = new \DateTime($conflictedLesson->date); 
					$lessonDate->setTime($hour, $minute, $second);
					$conflictedLesson->date = $lessonDate->format('Y-m-d H:i:s');
				} else {
					$lessonDate = new \DateTime($conflictedLesson->date); 
					$lessonDate->modify('next ' . $dayName);
					$lessonDate->setTime($hour, $minute, $second);
					$conflictedLesson->date = $lessonDate->format('Y-m-d H:i:s');	
				}
			} else {
				$conflictedLesson->status = Lesson::STATUS_UNSCHEDULED;
			}
			if(! $conflictedLesson->save()) {
				Yii::error('Resolve lesson conflict: ' . \yii\helpers\VarDumper::dumpAsString($conflictedLesson->getErrors()));
			}
		}
		$response = [
			'status' => true
		];
		return $response;
	}
    public function actionUpdateField($id)
    {
		$model = $this->findModel($id);
		$existingDate = $model->date;
        $data = $this->renderAjax('/lesson/review/_form', [
            'model' => $model,
        ]);
		$response = [
			'status' => true,
			'data' => $data
		];
        if ($model->load(Yii::$app->request->post()) && !empty($model->applyContext)) {
			if($model->isResolveSingleLesson()) {
				$response = $this->resolveSingleLesson($model, $existingDate);
			} else {
				$conflictedLessons = $this->fetchConflictedLesson($model->course);
				$response = $this->resolveAllLesson($conflictedLessons, $model);
			}
		} 
		return $response;
    }

    public function actionReview($courseId)
    {
		$model = new Lesson();
        $searchModel = new LessonSearch();
        $request = Yii::$app->request;
        $lessonSearchRequest = $request->get('LessonSearch');
        $showAllReviewLessons = $lessonSearchRequest['showAllReviewLessons'];
        $courseRequest = $request->get('Course');
        $enrolmentRequest = $request->get('Enrolment');
        $rescheduleBeginDate = $courseRequest['startDate'];
        $rescheduleEndDate = $courseRequest['endDate'];
		$enrolmentType = $enrolmentRequest['type'];
        $courseModel = Course::findOne(['id' => $courseId]);

		$conflictedLessons = $this->getConflicts($courseModel);
		$lessonCount = Lesson::find()
			->andWhere(['courseId' => $courseModel->id,	'isConfirmed' => false])
			->count();
		$conflictedLessonIdsCount = count($conflictedLessons['lessonIds']);
		$unscheduledLessonCount = Lesson::find()
			->where(['courseId' => $courseModel->id, 'status' => Lesson::STATUS_UNSCHEDULED, 'isConfirmed' => false])
			->count();	
		$query = Lesson::find()
			->orderBy(['lesson.date' => SORT_ASC]);
		if(! $showAllReviewLessons) {
			$query->andWhere(['IN', 'lesson.id', $conflictedLessons['lessonIds']]);
		}  else {
			$query->where(['courseId' => $courseModel->id, 'isConfirmed' => false]);
		}
        $lessonDataProvider = new ActiveDataProvider([
            'query' => $query,
			'pagination' => false,
        ]);
        return $this->render('review', [
            'courseModel' => $courseModel,
            'courseId' => $courseId,
            'lessonDataProvider' => $lessonDataProvider,
            'conflicts' => $conflictedLessons['conflicts'],
            'rescheduleBeginDate' => $rescheduleBeginDate,
            'rescheduleEndDate' => $rescheduleEndDate,
            'searchModel' => $searchModel,
			'model' => $model,
			'holidayConflictedLessonIds' => $courseModel->getHolidayLessons(),
			'lessonCount' => $lessonCount,
			'conflictedLessonIdsCount' => $conflictedLessonIdsCount,
			'unscheduledLessonCount' => $unscheduledLessonCount,
			'enrolmentType' => $enrolmentType,
        ]);
    }

    public function actionFetchConflict($courseId)
    {
        $response = \Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        $courseModel = Course::findOne(['id' => $courseId]);
		$conflicts = [];
		$conflictedLessonIds = [];
		$draftLessons = Lesson::find()
			->where(['courseId' => $courseModel->id, 'isConfirmed' => false,
				'status' => Lesson::STATUS_SCHEDULED])
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
		$holidayConflictedLessonIds = $courseModel->getHolidayLessons();
		$conflictedLessonIds = array_diff($conflictedLessonIds, $holidayConflictedLessonIds);
		$conflictedLessonIdsCount = count($conflictedLessonIds);
        $hasConflict = false;
		if ($conflictedLessonIdsCount > 0) {
            $hasConflict = true;
        }

        return [
            'hasConflict' => $hasConflict,
        ];
    }

    public function actionConfirm($courseId)
    {
        $courseModel = Course::findOne(['id' => $courseId]);
		$courseModel->updateAttributes([
			'isConfirmed' => true
		]);
		$holidayConflictedLessonIds = $courseModel->getHolidayLessons();
		$holidayLessons = Lesson::findAll(['id' => $holidayConflictedLessonIds]);
		foreach($holidayLessons as $holidayLesson) {
			$holidayLesson->updateAttributes([
				'status' => Lesson::STATUS_UNSCHEDULED
			]);
			$privateLessonModel = new PrivateLesson();
			$privateLessonModel->lessonId = $holidayLesson->id;
			$date = new \DateTime($holidayLesson->date);
			$expiryDate = $date->modify('90 days');
			$privateLessonModel->expiryDate = $expiryDate->format('Y-m-d H:i:s');
			$privateLessonModel->save();
		}
        $lessons = Lesson::findAll(['courseId' => $courseModel->id, 'isConfirmed' => false]);
		$lesson = end($lessons);
        $request = Yii::$app->request;
        $courseRequest = $request->get('Course');
        $enrolmentRequest = $request->get('Enrolment');
        $rescheduleEndDate = $courseRequest['endDate'];
        $rescheduleBeginDate = $courseRequest['startDate'];
        $enrolmentType = $enrolmentRequest['type'];
		if(!empty($enrolmentType)) {
			$courseModel->enrolment->student->updateAttributes([
				'status' => Student::STATUS_ACTIVE
			]);
			$courseModel->enrolment->student->customer->updateAttributes([
				'status' => User::STATUS_ACTIVE
			]);
		}
        if( ! empty($rescheduleBeginDate) && ! empty($rescheduleEndDate)) {
			$startDate = new \DateTime($rescheduleBeginDate);
			$endDate = new \DateTime($rescheduleEndDate);
			$oldLessons = Lesson::find()
				->where(['courseId' => $courseModel->id,
					'status' => Lesson::STATUS_SCHEDULED,
					'isConfirmed' => true])
				->between($startDate, $endDate)
				->all();
			$oldLessonIds = [];
			foreach($oldLessons as $oldLesson){
				$oldLessonIds[] = $oldLesson->id;
				$oldLesson->status = Lesson::STATUS_CANCELED;
				$oldLesson->save();
			}
			$courseDate = (new \DateTime($courseModel->endDate))->format('d-m-Y');	
			if($endDate->format('d-m-Y') == $courseDate && !empty($lesson)) {
				$courseModel->updateAttributes([
					'teacherId' => $lesson->teacherId,
				]);
				$courseModel->courseSchedule->updateAttributes([
					'day' => (new \DateTime($lesson->date))->format('N'),
					'fromTime' => (new \DateTime($lesson->date))->format('H:i:s'),
				]);
			}
		}
		if(! empty($rescheduleBeginDate)) {
			foreach ($lessons as $i => $lesson) {
				$lessonRescheduleModel = new LessonReschedule();
				$lessonRescheduleModel->lessonId = $oldLessonIds[$i];
				$lessonRescheduleModel->rescheduledLessonId = $lesson->id;
				if(!$lessonRescheduleModel->save()) {
					Yii::error('Bulk reschedule: ' . \yii\helpers\VarDumper::dumpAsString($lessonRescheduleModel->getErrors()));
				}
			if(! empty($rescheduleBeginDate)) {
				$bulkReschedule = new BulkReschedule();
				$bulkReschedule->type = $this->getRescheduleLessonType($courseModel, $rescheduleEndDate);
				try {
					$bulkReschedule->save();
				} catch(ErrorException $exception) {
					Yii::$app->errorHandler->logException($exception);
				}
				
				$bulkRescheduleLesson = new BulkRescheduleLesson();
				$bulkRescheduleLesson->bulkRescheduleId = $bulkReschedule->id;
				$bulkRescheduleLesson->lessonId = $lesson->id;
				try {
					$bulkRescheduleLesson->save();
				} catch(ErrorException $exception) {
					Yii::$app->errorHandler->logException($exception);
				}
			}
			}
		}
		foreach ($lessons as $lesson) {
			$lesson->updateAttributes([
				'isConfirmed' => true,
			]);
			$lesson->markAsRoot();
		}
        if (!empty($courseModel->enrolment) && empty($courseRequest)) {
            $enrolmentModel = Enrolment::findOne(['id' => $courseModel->enrolment->id]);
            $enrolmentModel->isConfirmed = true;
            $enrolmentModel->save();
            $enrolmentModel->setPaymentCycle($enrolmentModel->firstLesson->date);
            $user = User::findOne(['id' => Yii::$app->user->id]);
			$enrolmentModel->on(Enrolment::EVENT_CREATE,[new TimelineEventEnrolment(), 'create'], ['userName' => $user->publicIdentity]);
			$enrolmentModel->trigger(Enrolment::EVENT_CREATE);
        }
		if ($courseModel->program->isPrivate()) {
			if(! empty($rescheduleBeginDate)) {
				$message = 'Future lessons have been changed successfully';
				$link	 = $this->redirect(['enrolment/view', 'id' => $courseModel->enrolment->id]);
			} else {
				$invoice = $courseModel->enrolment->firstPaymentCycle->createProFormaInvoice();
				return $this->redirect(['/invoice/view', 'id' => $invoice->id]);
			}
		} else {
			$message = 'Course has been created successfully';
			$link	 = $this->redirect(['course/view', 'id' => $courseId]);
		}
		Yii::$app->session->setFlash('alert', [
			'options' => ['class' => 'alert-success'],
			'body' => $message,
		]);
		return $link;
	}
	public function getRescheduleLessonType($courseModel, $endDate) {
		$courseEndDate = (new \DateTime($courseModel->endDate))->format('d-m-Y');
		$type = BulkReschedule::TYPE_RESCHEDULE_FUTURE_LESSONS;	
		if($courseEndDate !== $endDate) {
			$type = BulkReschedule::TYPE_RESCHEDULE_BULK_LESSONS;	
		} 
		return $type;
	} 
    public function actionInvoice($id)
    {
        $model = Lesson::findOne(['id' => $id]);

        if ($model->canInvoice()) {
            if ($model->hasInvoice()) {
                $invoice = $model->invoice;
            } else {
                $invoice = $model->createPrivateLessonInvoice();
            }

            return $this->redirect(['invoice/view', 'id' => $invoice->id]);
        } else {
            Yii::$app->session->setFlash('alert', [
                'options' => ['class' => 'alert-danger'],
                'body' => 'Invoice can be generated against completed scheduled lessons only.',
            ]);

            return $this->redirect(['lesson/view', 'id' => $id]);
        }
    }

    public function actionTakePayment($id)
    {
        $model = Lesson::findOne(['id' => $id]);
        if ($model->paymentCycle) {
            $model->paymentCycle->setScenario(PaymentCycle::SCENARIO_CAN_RAISE_PFI);
            if (!$model->paymentCycle->validate()) {
                $errors	 = ActiveForm::validate($model->paymentCycle);
                Yii::$app->session->setFlash('alert', [
                    'options' => ['class' => 'alert-danger'],
                    'body' => end($errors['paymentcycle-id']),
                ]);
                return $this->redirect(['lesson/view', 'id' => $id]);
            }
            if(!$model->hasProFormaInvoice()) {
                if (!$model->paymentCycle->hasProFormaInvoice()) {
                    $invoice = $model->paymentCycle->createProFormaInvoice();

                    return $this->redirect(['invoice/view', 'id' => $invoice->id]);
                } else {
                    $model->addPrivateLessonLineItem($model->paymentCycle->proFormaInvoice);
                    $model->paymentCycle->proFormaInvoice->save();
                }
            } else {
                $model->proFormaInvoice->makeInvoicePayment($model);
            }
            return $this->redirect(['invoice/view', 'id' => $model->paymentCycle->proFormaInvoice->id]);
        } else if ($model->isExtra()) {
            if (!$model->hasProFormaInvoice()) {
                $locationId = $model->enrolment->student->customer->userLocation->location_id;
                $invoice = new Invoice();
                if (is_a(Yii::$app, 'yii\console\Application')) {
                    $roleUser = User::findByRole(User::ROLE_BOT);
                    $botUser = end($roleUser);
                    $loggedUser = User::findOne(['id' => $botUser->id]);
                } else {
                    $loggedUser = User::findOne(['id' => Yii::$app->user->id]);
                }
                $invoice->userName = $loggedUser->userProfile->fullName;
                $invoice->on(Invoice::EVENT_CREATE, [new InvoiceLog(), 'create']);
                $invoice->user_id = $model->enrolment->student->customer->id;
                $invoice->location_id = $locationId;
                $invoice->type = INVOICE::TYPE_PRO_FORMA_INVOICE;
                $invoice->createdUserId = Yii::$app->user->id;
                $invoice->updatedUserId = Yii::$app->user->id;
                $invoice->save();
                $invoiceLineItem = $model->addPrivateLessonLineItem($invoice);
                $invoice->save();
            } else {
                $invoice = $model->proFormaInvoice;
            }
            return $this->redirect(['invoice/view', 'id' => $invoice->id]);
        }
    }

    public function actionModifyClassroom($id, $classroomId)
    {
        $model = Lesson::findOne($id);
        $model->setScenario(Lesson::SCENARIO_EDIT_CLASSROOM);
        $model->classroomId = $classroomId;
        if ($model->validate()) {
            $model->save(false);
            $response = [
                'status' => true,
            ];
        } else {
            $model = ActiveForm::validate($model);
            $response = [
                'status' => false,
                'errors' => current($model),
            ];
        }

        return $response;
    }

    public function actionModifyLesson($id, $start, $end, $teacherId)
    {
        $model = Lesson::findOne($id);
        $model->setScenario(Lesson::SCENARIO_LESSON_EDIT_ON_SCHEDULE);
        $model->teacherId = $teacherId;
        $startDate = new \DateTime($start);
        $endDate = new \DateTime($end);
        $diff = date_diff($startDate, $endDate);
        $model->duration = $diff->format("%H:%I:%S");
        $model->date = $startDate->format('Y-m-d H:i:s');

        if ($model->validate()) {
            $model->save(false);
            $response = [
                'status' => true,
            ];
        } else {
            $errors = ActiveForm::validate($model);
            $response = [
                'status' => false,
                'errors' => current($errors),
            ];
        }

        return $response;
    }
    
    public function actionPayment($lessonId, $enrolmentId)
    {
        $payments = Payment::find()
                ->joinWith(['lessonCredit' => function($query) use($lessonId, $enrolmentId){
                        $query->andWhere(['lesson_payment.lessonId' => $lessonId, 
                            'lesson_payment.enrolmentId' => $enrolmentId]);	
                }]);
        $paymentsDataProvider = new ActiveDataProvider([
            'query' => $payments,
        ]);
        $data = $this->renderAjax('payment/view', [
            'paymentsDataProvider' => $paymentsDataProvider
        ]); 
        return [
            'status' => true,
            'data' => $data
        ];
    }
    
    public function actionSubstitute($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
            $model->date = (new \DateTime($model->date))->format('Y-m-d H:i:s');
            $model->save();
            $parentLesson = $model->parent()->one();
            $lessonRescheduleModel			= new LessonReschedule();
            $lessonRescheduleModel->lessonId	        = $parentLesson->id;
            $lessonRescheduleModel->rescheduledLessonId = $model->id;
            $lessonRescheduleModel->save();
            return [
                'status' => true
            ];
        } else {
            return [
                'status' => false,
                'errors' => ActiveForm::validate($model)
            ];
        }
    }
    
    public function actionUnschedule($id)
    {
        $model = $this->findModel($id);
        if ($model->unschedule()) {
            Yii::$app->session->setFlash('alert', [
                'options' => ['class' => 'alert-success'],
                'body' => 'Lesson unscheduled successfully!',
            ]);
        } else {
            Yii::$app->session->setFlash('alert', [
                'options' => ['class' => 'alert-danger'],
                'body' => 'Lesson cannot be unscheduled',
            ]);
        }
        return $this->redirect(['lesson/view', 'id' => $model->id]); 
    }
}
