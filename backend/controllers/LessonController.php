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
use common\models\TeacherAvailability;
use common\models\LessonLog;
use common\commands\AddToTimelineCommand;
use common\models\User;
use common\models\TimelineEvent;
use common\models\TimelineEventLink;
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
		$notes = Note::find()
			->where(['instanceId' => $model->id, 'instanceType' => Note::INSTANCE_TYPE_LESSON])
			->orderBy(['createdOn' => SORT_DESC]);

        $noteDataProvider = new ActiveDataProvider([
            'query' => $notes,
        ]);

		$groupLessonStudents = Student::find()
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
            $lessonDate = \DateTime::createFromFormat('Y-m-d g:i A', $model->date);
            $model->date = $lessonDate->format('Y-m-d H:i:s');
            $model->duration = $studentEnrolment->course->duration;
            
            if ($model->save()) {
                $model->addPaymentCycleLesson();
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
        if ($lessonDate < $currentDate) {
            Yii::$app->session->setFlash('alert', [
                'options' => ['class' => 'alert-danger'],
                'body' => 'Completed lessons cannot be editable!.',
            ]);

            return $this->redirect(['lesson/view', 'id' => $id, '#' => 'details']);
        }
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
            ->where(['lesson.id' => $id])->one();
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionUpdateField()
    {
		$request = Yii::$app->request;
		$response = Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        if ($request->post('hasEditable')) {
			$lessonId = $request->post('editableKey');
            $lessonIndex = $request->post('editableIndex');
            $model = Lesson::findOne(['id' => $lessonId]);
			$existingDate = $model->date;
            $result = [
                'output' => '',
                'message' => '',
            ];
			$posted = current($_POST['Lesson']);
        	$post = ['Lesson' => $posted];
            if ($model->load($post)) {
				if( ! empty($model->date)){
            		$model->setScenario(Lesson::SCENARIO_EDIT_REVIEW_LESSON);
				}
				if (isset($posted['date'])) {
					if(! empty($posted['date'])) {
						$model->date = (new \DateTime($posted['date']))->format('Y-m-d H:i:s');
						$output = Yii::$app->formatter->asDateTime($model->date);
					} else {
						$model->date = $existingDate;
						$model->status = Lesson::STATUS_UNSCHEDULED;
						$privateLessonModel = new PrivateLesson();
						$privateLessonModel->lessonId = $model->id;
						$date = new \DateTime($model->date);
						$expiryDate = $date->modify('90 days');
						$privateLessonModel->expiryDate = $expiryDate->format('Y-m-d H:i:s');
						$privateLessonModel->save();
						$output = '  ';
					}
				}
				if (!empty($posted['duration'])) {
					$model->duration = $posted['duration'];
					$output = $model->duration;
				}

             $success = $model->save();
			 $message = null;
			if (!$success) {
				$errors = ActiveForm::validate($model);
				$message = current($errors['lesson-date']);
            }
            $result = [
                'output' => $output,
                'message' => $message,
            ];
		}
            return $result;
        }
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
        $enrolment->setPaymentCycle();
		$invoice = $enrolment->firstPaymentCycle->createProFormaInvoice();
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
        $enrolmentRequest = $request->get('Enrolment');
        $endDate = $enrolmentRequest['endDate'];
        $enrolmentEditType = $enrolmentRequest['type'];
        $rescheduleBeginDate = $courseRequest['rescheduleBeginDate'];
        $vacationId = $vacationRequest['id'];
        $vacationType = $vacationRequest['type'];
        $courseModel = Course::findOne(['id' => $courseId]);
		$conflicts = [];
		$conflictedLessonIds = [];
		if(!empty($enrolmentEditType) && $enrolmentEditType === Enrolment::EDIT_LEAVE) {
			$lessons = Lesson::find()
				->where(['courseId' => $courseModel->id, 'lesson.status' => Lesson::STATUS_SCHEDULED])
				->andWhere(['>=', 'lesson.date', (new \DateTime($endDate))->format('Y-m-d')])
				->unInvoicedProForma()
				->all();
			foreach ($lessons as $lesson) {
				$conflicts[$lesson->id] = [];
			}
			$query = Lesson::find()
				->where(['courseId' => $courseModel->id, 'lesson.status' => Lesson::STATUS_SCHEDULED])
				->andWhere(['>=', 'lesson.date', (new \DateTime($endDate))->format('Y-m-d')])
				->unInvoicedProForma();
		} else {
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
			$query = Lesson::find()
				->orderBy(['lesson.date' => SORT_ASC]);
			if(! $showAllReviewLessons) {
				$query->andWhere(['IN', 'lesson.id', $conflictedLessonIds]);
			}  else {
					$query->where(['courseId' => $courseModel->id, 'status' => Lesson::STATUS_DRAFTED]);
				}
			}
        $lessonDataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        return $this->render('_review', [
            'courseModel' => $courseModel,
            'courseId' => $courseId,
            'lessonDataProvider' => $lessonDataProvider,
            'conflicts' => $conflicts,
            'rescheduleBeginDate' => $rescheduleBeginDate,
            'searchModel' => $searchModel,
			'vacationId' => $vacationId,
			'vacationType' => $vacationType,
			'endDate' => $endDate,
			'model' => $model,
			'enrolmentEditType' => $enrolmentEditType
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
        $hasConflict = false;
        foreach ($conflicts as $conflict) {
			if (!empty($conflict)) {
				$hasConflict = true;
				break;
			}
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
        $lessons = Lesson::findAll(['courseId' => $courseModel->id, 'status' => Lesson::STATUS_DRAFTED]);
        $request = Yii::$app->request;
        if (!empty($courseModel->enrolment)) {
            $enrolmentModel = Enrolment::findOne(['id' => $courseModel->enrolment->id]);
            $enrolmentModel->isConfirmed = true;
            $enrolmentModel->save();
			$dayList = Course::getWeekdaysList();
			$day = $dayList[$enrolmentModel->course->day];
			$staff = User::findOne(['id'=>Yii::$app->user->id]);
            $enrolment = Enrolment::find(['id' => $courseModel->enrolment->id])->asArray()->one();
			Yii::$app->commandBus->handle(new AddToTimelineCommand([
				'category' => 'enrolment',
				'event' => 'insert',
				'data' => $enrolment, 
				'message' => $staff->publicIdentity . ' enrolled ' . $enrolmentModel->student->fullName . ' in ' .  $enrolmentModel->course->program->name . ' lessons with ' . $enrolmentModel->course->teacher->publicIdentity . ' on ' . $day . 's at ' . Yii::$app->formatter->asTime($enrolmentModel->course->startDate),
        	]));
        }
        $courseRequest = $request->get('Course');
        $vacationRequest = $request->get('Vacation');
        $enrolmentRequest = $request->get('Enrolment');
        $rescheduleBeginDate = $courseRequest['rescheduleBeginDate'];
		$endDate = $enrolmentRequest['endDate'];
        $enrolmentEditType = $enrolmentRequest['type'];
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
		if(! empty($endDate)) {
			$courseModel->updateAttributes([
				'endDate' => (new \DateTime($endDate))->format('Y-m-d H:i:s'),
			]);
		}
		if(!empty($enrolmentEditType) && $enrolmentEditType === Enrolment::EDIT_LEAVE) {
			$lessons = Lesson::find()
				->where(['courseId' => $courseModel->id, 'lesson.status' => Lesson::STATUS_SCHEDULED])
				->andWhere(['>=', 'lesson.date', (new \DateTime($endDate))->format('Y-m-d')])
				->unInvoicedProForma()
				->all();
			foreach ($lessons as $lesson) {
				$lesson->updateAttributes([
					'isDeleted' => true,
				]);
			}
		} else {
			foreach ($lessons as $lesson) {
				$lesson->updateAttributes([
					'status' => Lesson::STATUS_SCHEDULED,
				]);
			}
		}
        if (!empty($courseModel->enrolment) && empty($courseRequest) &&
            empty($vacationRequest) && empty($enrolmentRequest)) {
            $enrolmentModel = Enrolment::findOne(['id' => $courseModel->enrolment->id]);
            $enrolmentModel->isConfirmed = true;
            $enrolmentModel->save();
            $enrolmentModel->setPaymentCycle();
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
			} elseif(! empty($endDate)) {
				$message = 'Your enrolment has been updated successfully';
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
        $lessonDate = \DateTime::createFromFormat('Y-m-d H:i:s', $model->date);
        $currentDate = new \DateTime();

        if ($lessonDate <= $currentDate) {
            $invoice = $model->createInvoice();

            return $this->redirect(['invoice/view', 'id' => $invoice->id]);
        } else {
            Yii::$app->session->setFlash('alert', [
                'options' => ['class' => 'alert-danger'],
                'body' => 'Generate invoice against completed lesson only.',
            ]);

            return $this->redirect(['lesson/view', 'id' => $id]);
        }
    }

	public function actionMissed($id)
	{
		$request = Yii::$app->request;
        $model = $this->findModel($id);
		$lessonRequest = $request->post('Lesson');
		if(!$lessonRequest['present']) {
			$model->status = Lesson::STATUS_MISSED;
			$model->save();
			$staff = User::findOne(['id'=>Yii::$app->user->id]);
            $lesson = Lesson::find(['id' => $model->id])->asArray()->one();
			$timelineEvent = Yii::$app->commandBus->handle(new AddToTimelineCommand([
				'category' => 'lesson',
				'event' => 'edit',
				'data' => $lesson, 
				'message' => $staff->publicIdentity . ' recorded {{' . $model->course->enrolment->student->fullName . '}} as absent from his/her ' . $model->course->program->name . ' {{lesson}}', 
        	]));
			
			$timelineEventLink = new TimelineEventLink();
			$timelineEventLink->timelineEventId = $timelineEvent->id;
			$timelineEventLink->index = $model->course->enrolment->student->fullName;
			$timelineEventLink->baseUrl = Yii::$app->homeUrl;	
			$timelineEventLink->path = Url::to(['/student/view', 'id' => $model->course->enrolment->student->id]);
			$timelineEventLink->save();
			$timelineEventLink->id = null;
			$timelineEventLink->isNewRecord = true;
			$timelineEventLink->index = 'lesson';
			$timelineEventLink->path = Url::to(['/lesson/view', 'id' => $model->id]);
			$timelineEventLink->save();
			
			if(empty($model->invoice)) {
				$invoice = $model->createInvoice();

				return $this->redirect(['invoice/view', 'id' => $invoice->id]);
			} else {
        	   return $this->redirect(['invoice/view', 'id' => $model->invoice->id]);
			}
		} else {
			$model->status = Lesson::STATUS_COMPLETED;
			$model->save();
			$model->invoice->delete();
		}
		
	}
	
	 public function actionTakePayment($id)
    {
        $model = Lesson::findOne(['id' => $id]);
        if(!$model->hasProFormaInvoice()) {
            if (!$model->paymentCycle->hasProFormaInvoice()) {
                $invoice = $model->paymentCycle->createProFormaInvoice();

                return $this->redirect(['invoice/view', 'id' => $invoice->id, '#' => 'payment']);
            } else {
                $model->paymentCycle->proFormaInvoice->addLineItem($model);
                $model->paymentCycle->proFormaInvoice->save();
            }
        } else {
            $model->proFormaInvoice->makeInvoicePayment();
        }
        return $this->redirect(['invoice/view', 'id' => $model->paymentCycle->proFormaInvoice->id, '#' => 'payment']);
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
					'body' => ' Mail has been send successfully',
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

	public function actionSplit($id) {
		$locationId = Yii::$app->session->get('location_id');
		$model = $this->findModel($id);
		$duration		 = \DateTime::createFromFormat('H:i:s', $model->duration);
		$hours			 = $duration->format('H');
		$minutes		 = $duration->format('i');
		$lessonDuration	 = ($hours * 60)  + $minutes;
		$request = Yii::$app->request;
		$lessonIds = $request->post('splitLessonIds');
        if (!empty($lessonIds) && is_array($lessonIds)) {
			$numberOfLesson = count($lessonIds);
			$duration = round($lessonDuration / $numberOfLesson);
			$lessons = Lesson::find()
				->location($locationId)
				->andWhere(['IN', 'lesson.id', $lessonIds])
				->all();
			foreach($lessons as $lesson) {
				$lesson->setScenario(Lesson::SCENARIO_SPLIT);
				$newDuration = new \DateTime($lesson->duration);
				$newDuration->add(new \DateInterval('PT' . $duration . 'M'));
				$lesson->newDuration = $newDuration; 
			}
			Model::validateMultiple($lessons);
			$conflicts = [];
			foreach($lessons as $lesson) {
				$conflicts = $lesson->getErrors('duration');
			}
			if(empty($conflicts)) {
				foreach($lessonIds as $lessonId) {
					$lesson = $this->findModel($lessonId);
					$newDuration = new \DateTime($lesson->duration);
					$newDuration->add(new \DateInterval('PT' . $duration . 'M'));	
					$lesson->updateAttributes([
						'duration' => $newDuration->format('H:i:s'),
					]);
				}
				$model->delete();
				Yii::$app->session->setFlash('alert', [
					'options' => ['class' => 'alert-success'],
					'body' => 'Lesson duration has been splitted successfully.',
				]);
				return $this->redirect(['index', 'LessonSearch[type]' => Lesson::TYPE_PRIVATE_LESSON]);
			} else {
				Yii::$app->session->setFlash('alert', [
					'options' => ['class' => 'alert-danger'],
					'body' => current($conflicts),
				]);
				return $this->redirect(['view', 'id' => $model->id]);	
			}
		}
	}
}
