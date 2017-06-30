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
use yii\web\Response;
use common\models\Vacation;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use common\models\timelineEvent\TimelineEventEnrolment;
use common\models\LessonLog;
use common\models\User;
use common\models\timelineEvent\TimelineEventLesson;
use yii\filters\ContentNegotiator;
use common\models\PaymentCycle;
use common\models\Invoice;
use common\models\InvoiceLog;
use common\models\LessonSplitUsage;
use common\models\LessonSplit;
use common\models\timelineEvent\VacationLog;
/**
 * LessonController implements the CRUD actions for Lesson model.
 */
class LessonController extends Controller
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
                'only' => ['modify-classroom', 'merge', 'update-field', 'modify-lesson'],
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
        $searchModel->lessonStatus = Lesson::STATUS_COMPLETED;
        $request = Yii::$app->request;
        $invoiceRequest = $request->get('LessonSearch');
        $searchModel->type = $invoiceRequest['type'];
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

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
        $locationId = Yii::$app->session->get('location_id');
        $model = $this->findModel($id);
        $model->duration = $model->fullDuration;
        $notes = Note::find()
                ->where(['instanceId' => $model->id, 'instanceType' => Note::INSTANCE_TYPE_LESSON])
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

        return $this->render('view', [
            'model' => $model,
			'noteDataProvider' => $noteDataProvider,
			'studentDataProvider' => $studentDataProvider
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
        $model->setScenario(Lesson::SCENARIO_CREATE);
        $request = Yii::$app->request;
        if ($model->load($request->post())) {
            $studentEnrolment = Enrolment::find()
                ->joinWith(['course' => function($query) use($model){
                    $query->where(['course.programId' => $model->programId]);
                }])
                ->where(['studentId' => $studentId])
                ->one();
            $model->courseId = $studentEnrolment->courseId;
            $model->status = Lesson::STATUS_SCHEDULED;
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
            return $response;
        }
    }

    public function actionValidate($studentId)
    {
		$response = \Yii::$app->response;
		$response->format = Response::FORMAT_JSON;
        $model = new Lesson();
		$model->setScenario(Lesson::SCENARIO_CREATE);
		$request = Yii::$app->request;
        if ($model->load($request->post())) {
			$studentEnrolment = Enrolment::find()
			   ->joinWith(['course' => function($query) use($model){
				   $query->where(['course.programId' => $model->programId]);
			   }])
				->where(['studentId' => $studentId])
				->one();
            $model->courseId = $studentEnrolment->courseId;
			return  ActiveForm::validate($model);
		}
    }

    /**
     * Updates an existing Lesson model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $oldLesson = clone $model;
        $oldDate = $model->date;
        $oldTeacherId = $model->teacherId;
        $user = User::findOne(['id'=>Yii::$app->user->id]);
        $model->userName = $user->publicIdentity;
        $model->on(Lesson::EVENT_RESCHEDULE_ATTEMPTED,
                [new LessonReschedule(), 'reschedule'], ['oldAttrtibutes' => $model->getOldAttributes()]);
        $model->on(Lesson::EVENT_RESCHEDULED,
                [new LessonLog(), 'reschedule'], ['oldAttrtibutes' => $model->getOldAttributes()]);
        $lessonDate = \DateTime::createFromFormat('Y-m-d H:i:s', $model->date);
        $currentDate = new \DateTime();

        $data = ['model' => $model];
        $view = '_form-group-lesson';
        if ($model->course->program->isPrivate()) {
            $view = '_form-private-lesson';
            if (!empty($model->privateLesson->id)) {
                $privateLessonModel = PrivateLesson::findOne(['lessonId' => $model->id]);
            } else {
                $privateLessonModel = new PrivateLesson();
            }

            if ($privateLessonModel->load(Yii::$app->request->post())) {
                $privateLessonModel->lessonId = $model->id;
                if (!empty($privateLessonModel->expiryDate)) {
                    $expiryDate = \DateTime::createFromFormat('d-m-Y g:i A', $privateLessonModel->expiryDate);
                    $privateLessonModel->expiryDate = $expiryDate->format('Y-m-d H:i:s');
                } else {
                    $privateLessonModel->expiryDate = $model->date;
                    $privateLessonModel->save();

                    return $this->redirect(['view', 'id' => $model->id, '#' => 'details']);
                }
                $privateLessonModel->save();
            }
            $data = ['model' => $model, 'privateLessonModel' => $privateLessonModel];
        }
        if ($model->load(Yii::$app->request->post())) {
			if(empty($model->date)) {
				$model->date =  $oldDate;
				$model->status = Lesson::STATUS_UNSCHEDULED;
				$model->save();
				$redirectionLink = $this->redirect(['view', 'id' => $model->id, '#' => 'details']);
			} else {
				if (new \DateTime($oldDate) != new \DateTime($model->date) || $oldTeacherId != $model->teacherId) {
					$model->setScenario(Lesson::SCENARIO_EDIT);
					$validate = $model->validate();
				}
				$lessonConflict = $model->getErrors('date');
				$message = current($lessonConflict);
				if(! empty($lessonConflict)){
					Yii::$app->session->setFlash('alert',
						[
						'options' => ['class' => 'alert-danger'],
						'body' => $message,
					]);
					$redirectionLink = $this->redirect(['update', 'id' => $model->id, '#' => 'details']);
				} else {
					if($model->course->program->isPrivate()) {
						$duration = \DateTime::createFromFormat('H:i', $model->duration);
						$model->duration = $duration->format('H:i:s');
					}
					$lessonDate = \DateTime::createFromFormat('d-m-Y g:i A', $model->date);
                                        if ($model->isExploded()) {
                                            $model->duration = $oldLesson->duration;
                                        }
					$model->date = $lessonDate->format('Y-m-d H:i:s');
                                        $model->save();

					$redirectionLink = $this->redirect(['view', 'id' => $model->id, '#' => 'details']);
				}
			}
            return $redirectionLink;
        }
        return $this->render($view, $data);
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
        $locationId = $session->get('location_id');
        $model = Lesson::find()->location($locationId)
            ->where(['lesson.id' => $id, 'isDeleted' => false])->one();
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionUpdateField($id)
    {
		$model = $this->findModel($id);
        $model->date = Yii::$app->formatter->asDateTime($model->date);
		$existingDate = $model->date;
        $data = $this->renderAjax('/lesson/review/_form', [
            'model' => $model,
        ]);
		$response = [
			'status' => true,
			'data' => $data
		];
        if ($model->load(Yii::$app->request->post())) {
			if(! empty($model->date)) {
           		$model->setScenario(Lesson::SCENARIO_EDIT_REVIEW_LESSON);
				$model->date = (new \DateTime($model->date))->format('Y-m-d H:i:s');
			} else {
				$model->date = $existingDate;
				$model->status = Lesson::STATUS_UNSCHEDULED;
				$privateLessonModel = new PrivateLesson();
				$privateLessonModel->lessonId = $model->id;
				$date = new \DateTime($model->date);
				$expiryDate = $date->modify('90 days');
				$privateLessonModel->expiryDate = $expiryDate->format('Y-m-d H:i:s');
				$privateLessonModel->save();
			}
 			if($model->save()) {
				$response = [
					'status' => true
				];
			} else {
				$response = [
					'status' => false,
					'errors' => ActiveForm::validate($model),
				];
			}
        }	
		 return $response;
    }

	public function actionGroupEnrolmentReview($courseId, $enrolmentId)
	{
		$model = new Lesson();
		$enrolment = Enrolment::findOne(['id' => $enrolmentId]);
        $searchModel = new LessonSearch();
		$request = Yii::$app->request;
        $lessonSearchRequest = $request->get('LessonSearch');
        $showAllReviewLessons = $lessonSearchRequest['showAllReviewLessons'];
        $courseModel = Course::findOne(['id' => $courseId]);
		$conflicts = [];
		$conflictedLessonIds = [];

		$lessons = Lesson::find()
			->where(['courseId' => $courseModel->id, 'status' => Lesson::STATUS_SCHEDULED])
			->all();
		foreach ($lessons as $lesson) {
			$lesson->setScenario(Lesson::SCENARIO_GROUP_ENROLMENT_REVIEW);
			$lesson->studentId = $enrolment->student->id;
		}
		Model::validateMultiple($lessons);
		foreach ($lessons as $lesson) {
			if(!empty($lesson->getErrors('date'))) {
				$conflictedLessonIds[] = $lesson->id;
			}
			$conflicts[$lesson->id] = $lesson->getErrors('date');
		}
		$query = Lesson::find()
			->orderBy(['lesson.date' => SORT_ASC]);
		if(! $showAllReviewLessons) {
			$query->andWhere(['IN', 'lesson.id', $conflictedLessonIds]);
		}  else {
				$query->where(['courseId' => $courseModel->id, 'status' => Lesson::STATUS_SCHEDULED]);
		}
        $lessonDataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $this->render('enrolment/_review', [
            'courseModel' => $courseModel,
            'courseId' => $courseId,
            'lessonDataProvider' => $lessonDataProvider,
            'conflicts' => $conflicts,
			'model' => $model,
            'searchModel' => $searchModel,
			'enrolment' => $enrolment,
        ]);
	}

	public function actionConfirmGroupEnrolment($enrolmentId)
	{
		$enrolment = Enrolment::findOne(['id' => $enrolmentId]);
		$enrolment->isConfirmed = true;
		$enrolment->save();
        $user = User::findOne(['id' => Yii::$app->user->id]);
        $enrolment->on(Enrolment::EVENT_GROUP, [new TimelineEventEnrolment(), 'groupCourseEnrolment'], ['userName' => $user->publicIdentity]);
        $enrolment->trigger(Enrolment::EVENT_GROUP);
        $invoice = $enrolment->createProFormaInvoice();
			return $this->redirect(['/invoice/view', 'id' => $invoice->id]);
	}

    public function actionReview($courseId)
    {
		$model = new Lesson();
        $searchModel = new LessonSearch();
        $request = Yii::$app->request;
        $lessonSearchRequest = $request->get('LessonSearch');
        $showAllReviewLessons = $lessonSearchRequest['showAllReviewLessons'];
        $vacationRequest = $request->get('Vacation');
        $courseRequest = $request->get('Course');
        $rescheduleBeginDate = $courseRequest['rescheduleBeginDate'];
        $vacationId = $vacationRequest['id'];
        $vacationType = $vacationRequest['type'];
        $courseModel = Course::findOne(['id' => $courseId]);
		$conflicts = [];
		$conflictedLessonIds = [];
			$draftLessons = Lesson::find()
				->where(['courseId' => $courseModel->id, 'status' => Lesson::STATUS_DRAFTED])
				->all();
		foreach ($draftLessons as $draftLesson) {
			$draftLesson->setScenario('review');
			if(!empty($vacationId)) {
				$draftLesson->vacationId = $vacationId;
			}
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
		$lessonCount = count($draftLessons);
		$conflictedLessonIdsCount = count($conflictedLessonIds);

		$query = Lesson::find()
			->orderBy(['lesson.date' => SORT_ASC]);
		if(! $showAllReviewLessons) {
			$query->andWhere(['IN', 'lesson.id', $conflictedLessonIds]);
		}  else {
			$query->where(['courseId' => $courseModel->id, 'status' => Lesson::STATUS_DRAFTED]);
		}
        $lessonDataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        return $this->render('review', [
            'courseModel' => $courseModel,
            'courseId' => $courseId,
            'lessonDataProvider' => $lessonDataProvider,
            'conflicts' => $conflicts,
            'rescheduleBeginDate' => $rescheduleBeginDate,
            'searchModel' => $searchModel,
			'vacationId' => $vacationId,
			'vacationType' => $vacationType,
			'model' => $model,
			'holidayConflictedLessonIds' => $holidayConflictedLessonIds,
			'lessonCount' => $lessonCount,
			'conflictedLessonIdsCount' => $conflictedLessonIdsCount
        ]);
    }

    public function actionFetchConflict($courseId)
    {
        $response = \Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        $courseModel = Course::findOne(['id' => $courseId]);
        $draftLessons = Lesson::find()
            ->where(['courseId' => $courseModel->id, 'status' => Lesson::STATUS_DRAFTED])
            ->all();
        foreach ($draftLessons as $draftLesson) {
            $draftLesson->setScenario('review');
        }
        Model::validateMultiple($draftLessons);
        $conflicts = [];
        foreach ($draftLessons as $draftLesson) {
            $conflicts[$draftLesson->id] = $draftLesson->getErrors('date');
        }
		$conflictedLessonIds = [];
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
        $lessons = Lesson::findAll(['courseId' => $courseModel->id, 'status' => Lesson::STATUS_DRAFTED]);
        $request = Yii::$app->request;
        $courseRequest = $request->get('Course');
        $vacationRequest = $request->get('Vacation');
        $rescheduleBeginDate = $courseRequest['rescheduleBeginDate'];
        $vacationId = $vacationRequest['id'];
        $vacationType = $vacationRequest['type'];
		if(! empty($vacationId)) {
			$vacation = Vacation::findOne(['id' => $vacationId]);
			$fromDate = (new \DateTime($vacation->fromDate))->format('Y-m-d');
			$toDate = (new \DateTime($vacation->toDate))->format('Y-m-d');
			if($vacationType === Vacation::TYPE_CREATE) {
				$vacation->isConfirmed = true;
				$vacation->save();
				$oldLessons = Lesson::find()
				->where(['courseId' => $courseId])
				->andWhere(['>=', 'lesson.date', $fromDate])
				->all();
				$oldLessonIds = [];
				foreach ($oldLessons as $oldLesson) {
					$oldLessonIds[] = $oldLesson->id;
					$oldLesson->status = Lesson::STATUS_CANCELED;
					$oldLesson->save();
				}
                $userModel = User::findOne(['id' => Yii::$app->user->id]);
                $vacation->on(Vacation::EVENT_CREATE, [new VacationLog(), 'create']);
                $vacation->userName = $userModel->publicIdentity;
                $vacation->trigger(Vacation::EVENT_CREATE); 
			} else {
				$oldLessons = Lesson::find()
				->where(['courseId' => $courseId])
				->andWhere(['>=', 'lesson.date', $toDate])
				->all();
				$oldLessonIds = [];
				foreach ($oldLessons as $oldLesson) {
					$oldLessonIds[] = $oldLesson->id;
					$oldLesson->status = Lesson::STATUS_CANCELED;
					$oldLesson->save();
				}
				$vacation->delete();
                $userModel = User::findOne(['id' => Yii::$app->user->id]);
                $vacation->on(Vacation::EVENT_DELETE, [new VacationLog(), 'deleteVacation']);
                $vacation->userName = $userModel->publicIdentity;
                $vacation->trigger(Vacation::EVENT_DELETE); 
			}
		}
        if( ! empty($rescheduleBeginDate)) {
			$courseDate = \DateTime::createFromFormat('d-m-Y',$rescheduleBeginDate);
			$courseDate = $courseDate->format('Y-m-d 00:00:00');
			$oldLessons = Lesson::find()
				->where(['courseId' => $courseModel->id, 'status' => Lesson::STATUS_SCHEDULED])
				->andWhere(['>=', 'date', $courseDate])
				->all();
			$oldLessonIds = [];
			foreach($oldLessons as $oldLesson){
				$oldLessonIds[] = $oldLesson->id;
				$oldLesson->status = Lesson::STATUS_CANCELED;
				$oldLesson->save();
			}
		}
		if(! empty($vacationId) || ! empty($rescheduleBeginDate)) {
			foreach ($lessons as $i => $lesson) {
				$lessonRescheduleModel = new LessonReschedule();
				$lessonRescheduleModel->lessonId = $oldLessonIds[$i];
				$lessonRescheduleModel->rescheduledLessonId = $lesson->id;
				$lessonRescheduleModel->save();
			}
		}
		foreach ($lessons as $lesson) {
			$lesson->updateAttributes([
				'status' => Lesson::STATUS_SCHEDULED,
			]);
		}
        if (!empty($courseModel->enrolment) && empty($courseRequest) &&
            empty($vacationRequest)) {
            $enrolmentModel = Enrolment::findOne(['id' => $courseModel->enrolment->id]);
            $enrolmentModel->isConfirmed = true;
            $enrolmentModel->save();
            $enrolmentModel->setPaymentCycle();
            $user = User::findOne(['id' => Yii::$app->user->id]);
			$enrolmentModel->on(Enrolment::EVENT_CREATE,[new TimelineEventEnrolment(), 'create'], ['userName' => $user->publicIdentity]);
			$enrolmentModel->trigger(Enrolment::EVENT_CREATE);
        }
		if ($courseModel->program->isPrivate()) {
			if (!empty($vacationId)) {
				if ($vacationType === Vacation::TYPE_CREATE) {
					$message = 'Vacation has been created successfully';
					$link	 = $this->redirect(['student/view', 'id' => $courseModel->enrolment->student->id, '#' => 'vacation']);
				} else {
					$message = 'Vacation has been deleted successfully';
					$link	 = $this->redirect(['student/view', 'id' => $courseModel->enrolment->student->id, '#' => 'vacation']);
				}
			} elseif(! empty($rescheduleBeginDate)) {
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
                'body' => 'Generate invoice against completed scheduled lesson only.',
            ]);

            return $this->redirect(['lesson/view', 'id' => $id]);
        }
    }

	public function actionMissed($id)
	{
        $model = $this->findModel($id);
		$model->on(Lesson::EVENT_MISSED, [new TimelineEventLesson(), 'missed']);
		$user = User::findOne(['id' => Yii::$app->user->id]);
		$model->userName = $user->publicIdentity;
		$model->status = Lesson::STATUS_MISSED;
		$model->save();
		$model->trigger(Lesson::EVENT_MISSED);
		if(empty($model->invoice)) {
			$invoice = $model->createPrivateLessonInvoice();
			return $this->redirect(['invoice/view', 'id' => $invoice->id]);
		} else {
		   return $this->redirect(['invoice/view', 'id' => $model->invoice->id]);
		}
	}

	public function actionPresent($id)
	{
        $model = $this->findModel($id);
		$currentDate = new \DateTime();
		$lessonDate = new \DateTime($model->date);
		$model->status = Lesson::STATUS_SCHEDULED;
		if($currentDate >= $lessonDate) {
			$model->status = Lesson::STATUS_COMPLETED;
		}
		$model->save();
	}

	public function actionAbsent($id)
	{
        $model = $this->findModel($id);
		$model->status = Lesson::STATUS_MISSED;
		$model->save();
		return [
			'status' => true,
		];
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
                    $model->paymentCycle->proFormaInvoice->addPrivateLessonLineItem($model);
                    $model->paymentCycle->proFormaInvoice->save();
                }
            } else {
                $model->proFormaInvoice->makeInvoicePayment();
            }
            return $this->redirect(['invoice/view', 'id' => $model->paymentCycle->proFormaInvoice->id]);
        } else if ($model->isExtra()) {
            if (!$model->hasProFormaInvoice()) {
                $locationId = $model->enrolment->student->customer->userLocation->location_id;
                $user = User::findOne(['id' => $model->enrolment->student->customer->id]);
                $invoice = new Invoice();
                $invoice->on(Invoice::EVENT_CREATE, [new InvoiceLog(), 'create']);
                $invoice->userName = $user->publicIdentity;
                $invoice->user_id = $model->enrolment->student->customer->id;
                $invoice->location_id = $locationId;
                $invoice->type = INVOICE::TYPE_PRO_FORMA_INVOICE;
                $invoice->createdUserId = Yii::$app->user->id;
                $invoice->updatedUserId = Yii::$app->user->id;
                $invoice->save();
                $invoiceLineItem = $invoice->addPrivateLessonLineItem($model);
                $invoice->save();
            } else {
                $invoice = $model->proFormaInvoice;
            }
            return $this->redirect(['invoice/view', 'id' => $invoice->id]);
        }
    }

    public function actionSendMail($id)
    {
        $model      = $this->findModel($id);
        $lessonRequest = Yii::$app->request->post('Lesson');
        if($lessonRequest) {
            $model->toEmailAddress = $lessonRequest['toEmailAddress'];
            $model->subject = $lessonRequest['subject'];
            $model->content = $lessonRequest['content'];
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
    }

    public function actionSplit($id)
    {
        $model = $this->findModel($id);
        $lessonDurationSec = $model->durationSec;
        if ($model->hasProFormaInvoice()) {
            $model->proFormaInvoice->removeLessonItem($id);
        }
        
        for ($i = 0; $i < $lessonDurationSec / Lesson::DEFAULT_EXPLODE_DURATION_SEC; $i++) {
            $lesssonSplit = new LessonSplit();
            $lesssonSplit->lessonId = $id;
            $lesssonSplit->unit = Lesson::DEFAULT_MERGE_DURATION;
            $lesssonSplit->save();
        }
        Yii::$app->session->setFlash('alert', [
            'options' => ['class' => 'alert-success'],
            'body' => 'The Lesson has been exploded successfully.',
        ]);
        return $this->redirect(['student/view', 'id' => $model->enrolment->student->id, '#'=> 'lesson']);
    }

    public function actionMerge($id)
    {
        $model = $this->findModel($id);
        $model->setScenario(Lesson::SCENARIO_EDIT);
        $post = Yii::$app->request->post();
        $additionalDuration = new \DateTime(Lesson::DEFAULT_MERGE_DURATION);
        $lessonDuration = new \DateTime($model->duration);
        $lessonDuration->add(new \DateInterval('PT' . $additionalDuration->format('H')
            . 'H' . $additionalDuration->format('i') . 'M'));
        $model->duration = $lessonDuration->format('H:i:s');
        if ($model->validate()) {
            $lessonSplitUsage = new LessonSplitUsage();
            $lessonSplitUsage->lessonSplitId = $post['radioButtonSelection'];
            $lessonSplitUsage->lessonSplitId = $lessonSplitUsage->getLessonSplitId();
            $lessonSplitUsage->extendedLessonId = $id;
            $lessonSplitUsage->mergedOn = (new \DateTime())->format('Y-m-d H:i:s');
            $lessonSplitUsage->save();
            Yii::$app->session->setFlash('alert', [
                'options' => ['class' => 'alert-success'],
                'body' => 'The Lesson has been extended successfully.',
            ]);

            return $this->redirect(['lesson/view', 'id' => $id]);
        } else {
            $errors = ActiveForm::validate($model);
            return [
                'errors' => $errors,
                'status' => false
            ];
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
        $model->setScenario(Lesson::SCENARIO_EDIT);
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
}
