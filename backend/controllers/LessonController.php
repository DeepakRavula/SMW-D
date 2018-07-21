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
use common\models\Location;
use common\models\log\LogHistory;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\Note;
use common\models\Student;
use yii\web\Response;
use common\models\Payment;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use common\models\User;
use yii\filters\ContentNegotiator;
use common\models\PaymentCycle;
use common\models\BulkRescheduleLesson;
use common\models\log\StudentLog;
use common\components\controllers\BaseController;
use yii\filters\AccessControl;
use common\models\LessonHierarchy;
use common\models\LessonPayment;

/**
 * LessonController implements the CRUD actions for Lesson model.
 */
class LessonController extends BaseController
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
                    'payment', 'substitute','update','unschedule', 'credit-transfer',
                    'edit-price', 'edit-tax', 'edit-cost'
                ],
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
                        'actions' => [
                            'index', 'view', 'credit-transfer', 'validate-on-update', 'edit-price',' edit-tax', 
							'fetch-duration','edit-classroom', 'update', 'update-field', 'review',
							'fetch-conflict', 'confirm', 'invoice', 'take-payment', 'modify-classroom',
                            'payment', 'substitute', 'unschedule', 'edit-cost', 'edit-tax', 
                        ],
                        'roles' => ['managePrivateLessons', 
							'manageGroupLessons'],
                    ],
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
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $model = $this->findModel($id);
        $enrolment = Enrolment::findOne(['courseId' => $model->courseId]);
        $model->duration = $model->fullDuration;
        $notes = Note::find()
                ->andWhere(['instanceId' => $model->id, 'instanceType' => Note::INSTANCE_TYPE_LESSON])
                ->orderBy(['createdOn' => SORT_DESC]);

        $noteDataProvider = new ActiveDataProvider([
            'query' => $notes,
        ]);

        $groupLessonStudents = Student::find()
            ->notDeleted()
            ->joinWith(['enrolment' => function ($query) use ($id) {
                $query->joinWith(['course' => function ($query) use ($id) {
                    $query->joinWith(['program' => function ($query) use ($id) {
                        $query->group();
                    }]);
                    $query->joinWith(['lessons' => function ($query) use ($id) {
                        $query->andWhere(['lesson.id' => $id]);
                    }])
                    ->confirmed();
                }])
                ->notDeleted()
                ->isConfirmed();
            }])
            ->location($locationId);

        $studentDataProvider = new ActiveDataProvider([
            'query' => $groupLessonStudents,
        ]);
        $payments = LessonPayment::find()
            ->joinWith(['payment' => function ($query) {
                $query->notDeleted();
        }])
        ->andWhere(['lesson_payment.lessonId' => $id])
        ->notDeleted();
        $paymentsDataProvider = new ActiveDataProvider([
            'query' => $payments
        ]);
        $logDataProvider = new ActiveDataProvider([
            'query' => LogHistory::find()->lesson($id) 
        ]);

        return $this->render('view', [
            'model' => $model,
            'noteDataProvider' => $noteDataProvider,
            'studentDataProvider' => $studentDataProvider,
            'paymentsDataProvider' => $paymentsDataProvider,
            'logDataProvider' => $logDataProvider,
        ]);
    }

    public function actionValidateOnUpdate($id, $teacherId = null)
    {
        $errors = [];
        $model = $this->findModel($id);
        $model->programId = $model->course->programId;
        if (empty($teacherId)) {
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
        $model->setScenario(Lesson::SCENARIO_EDIT_CLASSROOM);
        if ($model->load($request->post())) {
            if ($model->save()) {
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
        $model->date =Yii::$app->formatter->asDateTime($model->date);
        $user = User::findOne(['id'=>Yii::$app->user->id]);
        $model->userName = $user->publicIdentity;
        $model->on(
            Lesson::EVENT_RESCHEDULE_ATTEMPTED,
                [new LessonReschedule(), 'reschedule'],
            ['oldAttrtibutes' => $model->getOldAttributes()]
        );
        $data = $this->renderAjax('_form', [
            'model' => $model,
            'privateLessonModel' => $model->privateLesson
        ]);
        $request = Yii::$app->request;
        if ($request->post()) {
            $model->load($request->post());
            if ($model->hasExpiryDate()) {
                $privateLessonModel = PrivateLesson::findOne(['lessonId' => $model->id]);
                $privateLessonModel->load(Yii::$app->getRequest()->getBodyParams(), 'PrivateLesson');
                $privateLessonModel->expiryDate = (new \DateTime($privateLessonModel->expiryDate))->format('Y-m-d H:i:s');
                $privateLessonModel->save();
            }
            if (empty($model->date)) {
                $model->date =  $oldDate;
                $model->status = Lesson::STATUS_UNSCHEDULED;
                $model->save();
                $response = [
                    'status' => true,
                    'url' => Url::to(['lesson/view', 'id' => $model->id])
                ];
            } else {
                $model->setScenario(Lesson::SCENARIO_EDIT);
                if ($model->validate()) {
                    $model->status = Lesson::STATUS_CANCELED;
                    $duration = new \DateTime($model->duration);
                    $model->duration = $duration->format('H:i:s');
                    $lessonDate = new \DateTime($model->date);
                    $model->date = $lessonDate->format('Y-m-d H:i:s');
                    if ($model->save()) {
                        $response = [
                            'status' => true,
                            'url' => Url::to(['lesson/view', 'id' => $model->id])
                        ];
                    }
                } else {
                    $response = [
                        'status' => false,
                        'errors' => current(ActiveForm::validate($model))
                    ];
                }
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
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $model = Lesson::find()->location($locationId)
            ->andWhere(['lesson.id' => $id, 'isDeleted' => false])->one();
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
            ->andWhere(['courseId' => $course->id])
            ->notConfirmed()
            ->scheduled()
            ->all();
        foreach ($draftLessons as $draftLesson) {
            $draftLesson->setScenario('review');
        }
        Model::validateMultiple($draftLessons);
        foreach ($draftLessons as $draftLesson) {
            if (!empty($draftLesson->getErrors('date'))) {
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
            ->andWhere(['courseId' => $course->id])
            ->notConfirmed()
            ->scheduled()
            ->all();
        if (!empty($conflictedLessons['lessonIds'])) {
            $lessons = Lesson::find()
                ->orderBy(['lesson.date' => SORT_ASC])
                ->andWhere(['IN', 'lesson.id', $conflictedLessons['lessonIds']])
                ->all();
        }
        return $lessons;
    }
    public function resolveSingleLesson($lesson, $oldDate)
    {
        if (! empty($lesson->date)) {
            $lesson->setScenario(Lesson::SCENARIO_EDIT_REVIEW_LESSON);
            $lesson->date = (new \DateTime($lesson->date))->format('Y-m-d H:i:s');
        } else {
            $lesson->date = $oldDate;
            $lesson->status = Lesson::STATUS_UNSCHEDULED;
        }
        if ($lesson->save()) {
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
        foreach ($conflictedLessons as $conflictedLesson) {
            $conflictedLesson->duration = $lesson->duration;
            if (! empty($lesson->date)) {
                $day = (new \DateTime($lesson->date))->format('N');
                $lessonDay = (new \DateTime($conflictedLesson->date))->format('N');
                $time = (new \DateTime($lesson->date))->format('H:i:s');
                list($hour, $minute, $second) = explode(':', $time);
                $dayList = Course::getWeekdaysList();
                $dayName = $dayList[$day];
                if ($day === $lessonDay) {
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
            if (! $conflictedLesson->save()) {
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
            if ($model->isResolveSingleLesson()) {
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
            ->andWhere(['courseId' => $courseModel->id, 'status' => Lesson::STATUS_UNSCHEDULED, 'isConfirmed' => false])
            ->count();
        $query = Lesson::find()
            ->orderBy(['lesson.date' => SORT_ASC]);
        if (! $showAllReviewLessons) {
            $query->andWhere(['IN', 'lesson.id', $conflictedLessons['lessonIds']]);
        } else {
            $query->andWhere(['courseId' => $courseModel->id, 'isConfirmed' => false]);
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
            ->andWhere(['courseId' => $courseModel->id])
            ->notConfirmed()
            ->scheduled()
            ->all();
        foreach ($draftLessons as $draftLesson) {
            $draftLesson->setScenario('review');
        }
        Model::validateMultiple($draftLessons);
        foreach ($draftLessons as $draftLesson) {
            if (!empty($draftLesson->getErrors('date'))) {
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
        $loggedUser = User::findOne(['id' => Yii::$app->user->id]);
        $courseModel->updateAttributes([
            'isConfirmed' => true
        ]);
        $holidayConflictedLessonIds = $courseModel->getHolidayLessons();
        $holidayLessons = Lesson::findAll(['id' => $holidayConflictedLessonIds]);
        foreach ($holidayLessons as $holidayLesson) {
            $holidayLesson->updateAttributes([
                'status' => Lesson::STATUS_UNSCHEDULED
            ]);
        }
        $lessons = Lesson::find()
            ->andWhere(['courseId' => $courseModel->id, 'isConfirmed' => false])
            ->orderBy(['lesson.date' => SORT_ASC])
            ->all();
        $lesson = end($lessons);
        $request = Yii::$app->request;
        $courseRequest = $request->get('Course');
        $enrolmentRequest = $request->get('Enrolment');
        $rescheduleEndDate = $courseRequest['endDate'];
        $rescheduleBeginDate = $courseRequest['startDate'];
        $enrolmentType = $enrolmentRequest['type'];
        if (!empty($enrolmentType)) {
            $courseModel->enrolment->student->updateAttributes([
                'status' => Student::STATUS_ACTIVE
            ]);
            $courseModel->enrolment->student->customer->updateAttributes([
                'status' => User::STATUS_ACTIVE
            ]);
        }
        if (! empty($rescheduleBeginDate) && ! empty($rescheduleEndDate)) {
            $startDate = new \DateTime($rescheduleBeginDate);
            $endDate = new \DateTime($rescheduleEndDate);
            $oldLessons = Lesson::find()
                ->andWhere(['courseId' => $courseModel->id])
                ->notDeleted()
                ->isConfirmed()
                ->statusScheduled()
                ->andWhere(['>=', 'DATE(lesson.date)', $startDate->format('Y-m-d')])
                ->orderBy(['lesson.date' => SORT_ASC])
                ->all();
            $oldLessonIds = [];
            foreach ($oldLessons as $oldLesson) {
                $oldLessonIds[] = $oldLesson->id;
                $oldLesson->cancel();
            }
            $courseDate = (new \DateTime($courseModel->endDate))->format('d-m-Y');
            if ($endDate->format('d-m-Y') == $courseDate && !empty($lesson)) {
                $courseModel->updateAttributes([
                    'teacherId' => $lesson->teacherId,
                ]);
                $courseModel->courseSchedule->updateAttributes([
                    'day' => (new \DateTime($lesson->date))->format('N'),
                    'fromTime' => (new \DateTime($lesson->date))->format('H:i:s'),
                ]);
            }
        }
        if (empty($rescheduleBeginDate)) {
            foreach ($lessons as $lesson) {
                $lesson->makeAsRoot();
            }
        }
        if (! empty($rescheduleBeginDate)) {
            foreach ($lessons as $i => $lesson) {
                $oldLesson = Lesson::findOne($oldLessonIds[$i]);
                $oldLesson->rescheduleTo($lesson);
                LessonHierarchy::deleteAll($oldLesson->id);
                $bulkReschedule = new BulkRescheduleLesson();
                $bulkReschedule->lessonId = $lesson->id;
                $bulkReschedule->save();
            }
        }
        foreach ($lessons as $lesson) {
            $lesson->isConfirmed = true;
            $lesson->save();
            $lesson->setDiscount();
        }
        if (!empty($courseModel->enrolment) && empty($courseRequest)) {
            $enrolmentModel              = Enrolment::findOne(['id' => $courseModel->enrolment->id]);
            $enrolmentModel->isConfirmed = true;
            $enrolmentModel->save();
            $enrolmentModel->setPaymentCycle($enrolmentModel->firstLesson->date);
            $enrolmentModel->on(Enrolment::EVENT_AFTER_INSERT, [new StudentLog(), 'addEnrolment'],
                ['loggedUser' => $loggedUser]);
        }
        if ($courseModel->program->isPrivate()) {
            if (! empty($rescheduleBeginDate)) {
                $message = 'Future lessons have been changed successfully';
                $link	 = $this->redirect(['enrolment/view', 'id' => $courseModel->enrolment->id]);
            } else {
                if ($courseModel->enrolment->student->isDraft()) {
                    $courseModel->enrolment->student->updateAttributes(['status' => Student::STATUS_ACTIVE]);
                    $courseModel->enrolment->customer->updateAttributes(['status' => USER::STATUS_ACTIVE]);
                }
                $enrolmentModel->trigger(Enrolment::EVENT_AFTER_INSERT);
                return $this->redirect(['/enrolment/view', 'id' => $enrolmentModel->id]);
            }
        } else {
            $message = 'Course has been created successfully';
            $link = $this->redirect(['course/view', 'id' => $courseId]);
        }
        Yii::$app->session->setFlash('alert', [
            'options' => ['class' => 'alert-success'],
            'body' => $message,
        ]);
        return $link;
    }
    
    public function getRescheduleLessonType($courseModel, $endDate)
    {
        $courseEndDate = (new \DateTime($courseModel->endDate))->format('d-m-Y');
        $type = BulkReschedule::TYPE_RESCHEDULE_FUTURE_LESSONS;
        if ($courseEndDate !== $endDate) {
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
        }
        $invoice = $model->takePayment();
        return $this->redirect(['invoice/view', 'id' => $invoice->id]);
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

    public function actionPayment($lessonId, $enrolmentId)
    {
        $payments = Payment::find()
                ->joinWith(['lessonCredit' => function ($query) use ($lessonId, $enrolmentId) {
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
            return [
                'status' => true,
                'message'=>'Lesson unscheduled successfully',
            ];
        } else {
            return [
                'status' => false,
                'message' => 'Lesson cannot be unscheduled',
            ];
        }
    }

    public function actionCreditTransfer($id)
    {
        $model = $this->findModel($id);
        if ($model->hasInvoice()) {
            $model->creditTransfer($model->invoice);
            return [
                'status' => true,
                'message' => 'Lesson credits successfully transferd to invoice',
            ];
        } else {
            return [
                'status' => false,
                'message' => 'Lesson not yet invoiced',
            ];
        }
    }
    
    public function actionEditPrice($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        if ($request->isPost) {
            if ($model->load($request->post())) {
                if ($model->save()) {
                    $response = [
                        'status' => true
                    ];
                } else {
                    $response = [
                        'status' => false,
                        'errors' => ActiveForm::validate($model)
                    ];
                }
            }
        } else {
            $data = $this->renderAjax('_price-form', [
                'model' => $model
            ]);
            $response = [
                'status' => true,
                'data' => $data
            ];
        }
        return $response;
    }

    public function actionEditTax($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        $model->setScenario(Lesson::SCENARIO_EDIT);
        if ($request->isPost) {
            if ($model->load($request->post())) {
                $model->save();
                $response = [
                    'status' => true
                ];
            } 
        } else {
            $data = $this->renderAjax('_tax-form', [
                'model' => $model
            ]);    
            $response = [
                'status' => empty(current(ActiveForm::validate($model))),
                'data' => $data,
                'errors' => current(ActiveForm::validate($model))
            ];
        }
        return $response;
    }

    public function actionEditCost($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        if ($request->isPost) {
            if ($model->load($request->post())) {
                if ($model->save()) {
                    $response = [
                        'status' => true
                    ];
                } else {
                    $response = [
                        'status' => false,
                        'errors' => ActiveForm::validate($model)
                    ];
                }
            }
        } else {
            $data = $this->renderAjax('/lesson/cost/_form', [
                'model' => $model
            ]);
            $response = [
                'status' => true,
                'data' => $data
            ];
        }
        return $response;
    }
}
