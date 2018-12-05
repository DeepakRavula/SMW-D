<?php

namespace backend\controllers;

use common\models\Location;
use Yii;
use common\models\PaymentCycle;
use common\models\Enrolment;
use common\models\Lesson;
use common\models\Course;
use yii\data\ActiveDataProvider;
use backend\models\search\EnrolmentSearch;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use common\models\Label;
use backend\models\EnrolmentForm;
use common\models\Student;
use yii\filters\ContentNegotiator;
use common\models\log\LogHistory;
use common\models\log\EnrolmentLog;
use common\models\PaymentFrequency;
use common\models\UserPhone;
use common\models\UserAddress;
use common\models\UserEmail;
use common\models\UserContact;
use common\models\UserLocation;
use common\models\User;
use common\models\UserProfile;
use Carbon\Carbon;
use common\models\CourseReschedule;
use backend\models\discount\MultiEnrolmentDiscount;
use backend\models\discount\PaymentFrequencyEnrolmentDiscount;
use common\models\log\StudentLog;
use common\models\log\DiscountLog;
use yii\widgets\ActiveForm;
use yii\data\ArrayDataProvider;
use common\components\controllers\BaseController;
use yii\filters\AccessControl;
use backend\models\search\EnrolmentPaymentSearch;
use common\models\CustomerReferralSource;
use common\models\CourseSchedule;
use backend\models\GroupCourseForm;
use common\models\discount\EnrolmentDiscount;

/**
 * EnrolmentController implements the CRUD actions for Enrolment model.
 */
class EnrolmentController extends BaseController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    
                ],
            ],
            'contentNegotiator' => [
                'class' => ContentNegotiator::className(),
                'only' => ['add', 'delete', 'edit', 'schedule', 'group', 'update', 'full-delete',
                    'edit-end-date', 'edit-program-rate', 'reschedule', 'update-preferred-payment-status',
                    'group-confirm', 'group-enrolment-delete', 'group-apply', 'group-preview'
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
                        'actions' => ['index', 'view', 'group', 'edit', 'edit-program-rate', 
                            'create', 'add', 'confirm', 'update', 'delete', 'edit-end-date',
                            'reschedule', 'cancel', 'update-preferred-payment-status', 'group-confirm', 
                            'group-enrolment-delete', 'group-apply', 'group-preview'
                        ],
                        'roles' => ['manageEnrolments'],
                    ],
                    [
                        'allow' => true,
                        'actions' => [
                            'full-delete'
                        ],
                        'roles' => ['administrator'],
                    ],
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
        $scheduleHistoryDataProvider = new ActiveDataProvider([
            'query' => CourseSchedule::find()
            ->andWhere(['courseId' => $model->courseId]),
        ]);
	    $lessonCount = Lesson::find()
			->andWhere(['courseId' => $model->course->id])
            ->notDeleted()
            ->scheduledOrRescheduled()
            ->notCompleted()
			->count();
	    $lessonDataProvider = new ActiveDataProvider([
            'query' => Lesson::find()
                ->andWhere(['courseId' => $model->course->id])
                ->scheduledOrRescheduled()
                ->isConfirmed()
		        ->limit(12)
                ->notDeleted()
                ->notCompleted()
                ->orderBy(['lesson.date' => SORT_ASC]),
            'pagination' => false,
        ]);
        $groupLessonDataProvider = new ActiveDataProvider([
            'query' => Lesson::find()
                ->andWhere(['courseId' => $model->course->id])
                ->scheduledOrRescheduled()
                ->isConfirmed()
                ->notDeleted()
                ->notCompleted()
                ->orderBy(['lesson.date' => SORT_ASC]),
            'pagination' =>  [ 'pageSize' => 10, ],
        ]);
        $logDataProvider = new ActiveDataProvider([
            'query' => LogHistory::find()
            ->enrolment($id) ]);
       
        $paymentCycleDataProvider = new ActiveDataProvider([
            'query' => PaymentCycle::find()
                ->notDeleted()
                ->andWhere([
                    'enrolmentId' => $id,
                ]),
            'pagination' => false,
        ]);
        
        return $this->render('view', [
            'model' => $model,
	        'groupLessonDataProvider' => $groupLessonDataProvider,
            'lessonDataProvider' => $lessonDataProvider,
            'paymentCycleDataProvider' => $paymentCycleDataProvider,
            'logDataProvider' => $logDataProvider,
            'scheduleHistoryDataProvider' => $scheduleHistoryDataProvider,
	        'lessonCount' => $lessonCount,
        ]);
    }

    public function actionGroup()
    {
        $model = new GroupCourseForm();
        $model->load(Yii::$app->request->get());
        $unconfirmedEnrolments = Enrolment::find()
            ->notConfirmed()
            ->all();
        foreach ($unconfirmedEnrolments as $unconfirmedEnrolment) {
            $unconfirmedEnrolment->delete();
        }
        $post = Yii::$app->request->post();
        $model->load($post);
        $model->discountType = EnrolmentDiscount::VALUE_TYPE_DOLLAR;
        $data = $this->renderAjax('/student/enrolment/_form-group-discount', [
            'model' => $model
        ]);
        return [
            'status' => true,
            'data' => $data
        ];
    }

    public function actionGroupApply()
    {
        $model = new GroupCourseForm();
        $model->load(Yii::$app->request->get());
        $enrolmentModel = new Enrolment();
        $enrolmentModel->studentId = $model->studentId;
        $enrolmentModel->paymentFrequencyId = PaymentFrequency::LENGTH_FULL;
        $enrolmentModel->isConfirmed = false;
        $enrolmentModel->courseId = $model->courseId;
        $course = Course::findOne($model->courseId);
        $post = Yii::$app->request->post();
        if ($model->load($post)) {
            $enrolmentModel->studentId = $model->studentId;
            if ($enrolmentModel->save()) {
                $model->enrolmentId = $enrolmentModel->id;
                $model->setDiscount();
                $lessonDataProvider = new ActiveDataProvider([
                    'query' => Lesson::find()
                        ->andWhere(['courseId' => $course->id])
                        ->notCanceled()
                        ->isConfirmed()
                        ->notDeleted()
                        ->orderBy(['lesson.date' => SORT_ASC]),
                    'pagination' => false,
                ]);
                $data = $this->renderAjax('/student/enrolment/_group-enrolment-preview', [
                    'model' => $enrolmentModel,
                    'lessonDataProvider' => $lessonDataProvider
                ]);
                return $this->actionGroupPreview($enrolmentModel->id);
            }
        }
    }

    public function actionGroupPreview($enrolmentId)
    {
        $enrolmentModel = Enrolment::findOne($enrolmentId);
        $lessonDataProvider = new ActiveDataProvider([
            'query' => Lesson::find()
                ->andWhere(['courseId' => $enrolmentModel->course->id])
                ->notCanceled()
                ->isConfirmed()
                ->notDeleted()
                ->orderBy(['lesson.date' => SORT_ASC]),
            'pagination' => false,
        ]);
        $data = $this->renderAjax('/student/enrolment/_group-enrolment-preview', [
            'model' => $enrolmentModel,
            'lessonDataProvider' => $lessonDataProvider
        ]);
        return [
            'status' => true,
            'data' => $data
        ];
    }

    public function actionGroupConfirm($enrolmentId)
    {
        $enrolmentModel = Enrolment::findOne($enrolmentId);
        $enrolmentModel->isConfirmed = true;
        $post = Yii::$app->request->post();
        if ($post) {
            if ($course->hasExtraCourse()) {
                foreach ($course->extraCourses as $extraCourse) {
                    $extraCourse->studentId = $model->studentId;
                    $enrolment = $extraCourse->createExtraLessonEnrolment();
                }
            }
            if ($enrolmentModel->save()) {
                $enrolmentModel->setStatus();
                $loggedUser = User::findOne(['id' => Yii::$app->user->id]);
                $enrolmentModel->on(Enrolment::EVENT_AFTER_INSERT,
                    [new StudentLog(), 'addGroupEnrolment'],
                    ['loggedUser' => $loggedUser]
                );
                $enrolmentModel->trigger(Enrolment::EVENT_AFTER_INSERT);
                return [
                    'status' => true,
                    'url' => Url::to(['/course/view', 'id' => $course->id])
                ];
            }
        }
    }

    public function actionEdit($id)
    {
        $model = $this->findModel($id);
        $model->setScenario(Enrolment::SCENARIO_EDIT);
        if (!$model->validate()) {
            return [
                'status' => false,
                'message' => ActiveForm::validate($model)['enrolment-courseid']
            ];
        }
        $paymentFrequencyDiscount = new PaymentFrequencyEnrolmentDiscount();
        $multipleEnrolmentDiscount = new MultiEnrolmentDiscount();
        $oldMultipleEnrolmentDiscount = $model->multipleEnrolmentDiscount ? clone $model->multipleEnrolmentDiscount : null;
        $oldMultipleEnrolmentDiscountValue = $model->getMultipleEnrolmentDiscountValue();

        if ($model->hasMultiEnrolmentDiscount()) {
            $multipleEnrolmentDiscount = $multipleEnrolmentDiscount->setModel($model->multipleEnrolmentDiscount);
        }
        if ($model->hasPaymentFrequencyDiscount()) {
            $paymentFrequencyDiscount = $paymentFrequencyDiscount->setModel($model->paymentFrequencyDiscount);
        }
        $paymentFrequencyDiscount->enrolmentId = $id;
        $multipleEnrolmentDiscount->enrolmentId = $id;
        
        $previewDataProvider = $model->getPreviewDataProvider();
        
        $data = $this->renderAjax('update/_form', [
            'model' => $model,
            'multipleEnrolmentDiscount' => $multipleEnrolmentDiscount,
            'paymentFrequencyDiscount' => $paymentFrequencyDiscount,
            'previewDataProvider' => $previewDataProvider
        ]);
        $oldPaymentFrequency = clone $model;
        $oldPaymentFrequencyDiscount = $model->paymentFrequencyDiscount ? clone $model->paymentFrequencyDiscount : null;
        $oldPaymentFrequencyDiscountValue  = $model->getPaymentFrequencyDiscountValue();
        $post = Yii::$app->request->post();
        if ($post) {
            $paymentFrequencyDiscount->load($post);
            $multipleEnrolmentDiscount->load($post);
            $model->load($post);
            $multipleEnrolmentDiscount->save();
            $loggedUser = User::findOne(['id' => Yii::$app->user->id]);
            $paymentFrequencyDiscount->save();
            if (!$oldMultipleEnrolmentDiscount || ($oldMultipleEnrolmentDiscount->discount != $multipleEnrolmentDiscount->discount)) {
                $model->resetDiscount($multipleEnrolmentDiscount->type, !$multipleEnrolmentDiscount->discount ? 0.0 : $multipleEnrolmentDiscount->discount);
                $model->on(Enrolment::EVENT_AFTER_UPDATE, [new DiscountLog(), 'enrolmentMultipleDiscountEdit'],
                    ['loggedUser' => $loggedUser, 'oldDiscount' => $oldMultipleEnrolmentDiscountValue,'newDiscount' => $multipleEnrolmentDiscount->discount]
                );
            }
            if (!$oldPaymentFrequencyDiscount || ($oldPaymentFrequencyDiscount->discount != $paymentFrequencyDiscount->discount)) {
                $model->resetDiscount($paymentFrequencyDiscount->type, !$paymentFrequencyDiscount->discount ? 0.0 : $paymentFrequencyDiscount->discount);
                $model->on(Enrolment::EVENT_AFTER_UPDATE, [new DiscountLog(), 'enrolmentPaymentFrequencyDiscountEdit'],
                    ['loggedUser' => $loggedUser, 'oldDiscount' => $oldPaymentFrequencyDiscountValue,'newDiscount'=>$paymentFrequencyDiscount->discount]
                );
            }
            if ($model->save()) {
                if ((int) $oldPaymentFrequency->paymentFrequencyId !== (int) $model->paymentFrequencyId) {
                    $model->resetPaymentCycle();
                    $model->resetPaymentRequest();
                }
            }
            $response = [
                'status' => true
            ];
        } else {
            $response = [
                'status' => true,
                'data' => $data
            ];
        }
        return $response;
    }
    
    public function actionEditProgramRate($id)
    {
        $model = $this->findModel($id);
        $post = Yii::$app->request->post();
        if ($model->load($post)) {
            foreach ($model->courseProgramRates as $key => $courseProgramRate) {
                $courseProgramRate->load($post['CourseProgramRate'][$key], '');
                $courseProgramRate->save();
            }
            $oldAttributes = $model->getOldAttributes();
            if ($oldAttributes['isAutoRenew']!=$model->isAutoRenew) {
                $loggedUser = User::findOne(['id' => Yii::$app->user->id]);
                $model->on(Enrolment::EVENT_AFTER_UPDATE, [new EnrolmentLog(), 'editAutoRenewFeature'], ['loggedUser' => $loggedUser, 'autoRenewFeature' => $model->isAutoRenew]);
            }
            $model->save();
            $message = 'Details successfully updated!';
            return [
                'status' => true,
                'message' => $message,
            ];
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

    public function createUserContact($userId, $labelId)
    {
        $userContact = new UserContact();
        $userContact->userId = $userId;
        if (!is_numeric($labelId)) {
            $label = new Label();
            $label->name = $labelId;
            $label->userAdded = $userId;
            $label->save();
            $userContact->labelId = $label->id;
        } else {
            $userContact->labelId = $labelId;
        }
        $userContact->isPrimary = false;
        $userContact->save();
        return $userContact;
    }
    
    public function actionAdd()
    {
        $courseDetailData = Yii::$app->request->get('EnrolmentForm');
        $courseDetail = new EnrolmentForm();
        if ($courseDetailData) {
            $courseDetail->load(Yii::$app->request->get());
        }
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $user = new User();
        $userProfile = new UserProfile();
        $phoneNumber = new UserPhone();
        $address = new UserAddress();
        $userEmail = new UserEmail();
        $userLocation = new UserLocation();
        $student = new Student();
        $customerReferralSource = new CustomerReferralSource();
            
        $userProfile->setModel($courseDetail);
        $phoneNumber->setModel($courseDetail);
        $address->setModel($courseDetail);
        $userEmail->setModel($courseDetail);
        $student->setModel($courseDetail);
        $customerReferralSource->setModel($courseDetail);
        $user->status = User::STATUS_NOT_ACTIVE;
        $user->canLogin = true;
        $user->isDeleted = true;
        if ($user->save()) {
            $auth = Yii::$app->authManager;
            $authManager = Yii::$app->authManager;
            $authManager->assign($auth->getRole(User::ROLE_CUSTOMER), $user->id);
            $userProfile->user_id = $user->id;
            $userProfile->save();
            $userLocation->location_id = $locationId;
            $userLocation->user_id = $user->id;
            $userLocation->save();
            $userContact = $this->createUserContact($user->id, $userEmail->labelId);
            $userEmail->userContactId = $userContact->id;
            $userEmail->save();
            if($customerReferralSource->referralSourceId) {
            $customerReferralSource->userId = $user->id;
            $customerReferralSource->save();
            }
            
            //save address and phone number
            if (!empty($address->address)) {
                $userContact = $this->createUserContact($user->id, $address->labelId);
                $address->userContactId = $userContact->id;
                $address->save();
            }
            if (!empty($phoneNumber->number)) {
                $userContact = $this->createUserContact($user->id, $phoneNumber->labelId);
                $phoneNumber->userContactId = $userContact->id;
                $phoneNumber->save();
            }
            //save student
            $student->customer_id = $user->id;
            $student->status = Student::STATUS_INACTIVE;
            $student->isDeleted = true;
            $student->save();

            //save course
            return $this->redirect(['student/create-enrolment', 'id' => $student->id, 'EnrolmentForm' => $courseDetail]);
        }
    }

    public function actionConfirm($courseId)
    {
        $courseModel = Course::findOne(['id' => $courseId]);
        $courseModel->updateAttributes([
            'isConfirmed' => true
        ]);
        $lessons = Lesson::findAll(['courseId' => $courseModel->id, 'isConfirmed' => false]);
        foreach ($lessons as $lesson) {
            $lesson->updateAttributes([
                'status' => Lesson::STATUS_SCHEDULED,
            ]);
        }
        if (!empty($courseModel->enrolment)) {
            $enrolmentModel = Enrolment::findOne(['id' => $courseModel->enrolment->id]);
            $enrolmentModel->isConfirmed = true;
            $enrolmentModel->save();
            $enrolmentModel->setPaymentCycle($enrolmentModel->firstLesson->date);
        }
        Yii::$app->session->setFlash('alert', [
            'options' => ['class' => 'alert-success'],
            'body' => 'Enrolment has been created successfully',
        ]);
        return $this->redirect(['student/view', 'id' => $enrolmentModel->student->id]);
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
        $courseDetailData = Yii::$app->request->get('CourseReschedule');
        $model = $this->findModel($id);
        $courseReschedule = new CourseReschedule(['scenario' => CourseReschedule::SCENARIO_BASIC]);
        if ($courseDetailData) {
            $courseReschedule->load(Yii::$app->request->get());
        }
        $courseReschedule->setModel($model->course);
        $data = $this->renderAjax('/enrolment/bulk-reschedule/_form-basic', [
            'courseReschedule' => $courseReschedule,
            'model' => $model
        ]);
        $response = [
            'status' => true,
            'data' => $data
        ];
        if (Yii::$app->request->isPost) {
            if ($courseReschedule->load(Yii::$app->request->post()) && $courseReschedule->validate()) {
                $courseReschedule->setScenario($courseReschedule::SCENARIO_DETAILED);
               
                $courseRescheduleData = $this->renderAjax('/enrolment/bulk-reschedule/_form-detail', [
                    'courseReschedule' => $courseReschedule,
                    'course' => $model->course,
                    'courseSchedule' => $model->course->recentCourseSchedule,
                    'model' => $model
                ]);
                $response = [
                    'status' => true,
                    'data' => $courseRescheduleData
                ];
            } else {
                $response = [
                    'status' => false,
                    'errors' => ActiveForm::validate($courseReschedule)
                ];
            }
        }
        return $response;
    }

    public function actionReschedule($id)
    {
        $model = $this->findModel($id);
        $courseReschedule = new CourseReschedule();
        $course = $model->course;
        $courseReschedule->setModel($course);
        if (Yii::$app->request->isPost) {
            $courseReschedule->load(Yii::$app->request->post());
            $startDate = new \DateTime($courseReschedule->dateToChangeSchedule);
            $endDate = new \DateTime($model->endDate);
            if ($courseReschedule->validate()) {
                $isTeacherOnlyChanged = false;
                $day = (new \DateTime($courseReschedule->dayTime))->format('N');
                $fromTime = (new \DateTime($courseReschedule->dayTime))->format('H:i:s');
                if ($day == $course->recentCourseSchedule->day && $fromTime == $course->recentCourseSchedule->fromTime && $courseReschedule->teacherId != $course->teacherId) {
                    $isTeacherOnlyChanged = true;
                }
                $lastLessonDate = $courseReschedule->reschdeule();
                $rescheduleBeginDate = $startDate->format('d-m-Y');
                $rescheduleEndDate = (new \DateTime($lastLessonDate))->format('d-m-Y');
                $url = Url::to(['/lesson/review', 'LessonReview[courseId]' => $course->id, 'LessonReview[isTeacherOnlyChanged]' => $isTeacherOnlyChanged,
                    'LessonSearch[showAllReviewLessons]' => false, 'LessonReview[rescheduleBeginDate]' => $rescheduleBeginDate,
                    'LessonReview[rescheduleEndDate]' => $rescheduleEndDate]);
                $model->setStatus();
                $response = [
                    'status' => true,
                    'url' => $url
                ];
            } else {
                $response = [
                    'status' => false,
                    'errors' => ActiveForm::validate($courseReschedule)
                ];
            }
        }
        return $response;
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if ($model->course->program->isPrivate() && $model->canDeleted()) {
            $lessons = Lesson::find()
                ->andWhere(['courseId' => $model->courseId])
                ->isConfirmed()
                ->notCanceled()
                ->all();
            $message = null;
            $model->revertLessonsCredit($lessons);
            $message = 'Lesson credits has been credited to ' . $model->customer->publicIdentity . ' account.';
            $model->deleteWithOutTransactionalData();
            $model->setStatus();
            $response = [
                'status' => true,
                'url' => Url::to(['enrolment/index', 'EnrolmentSearch[showAllEnrolments]' => false]),
                'message' => $message
            ];
        } else {
            $response = [
                'status' => false
            ];
        }
        return $response;
    }

    public function actionFullDelete($id)
    {
        $model = $this->findModel($id);
        $student = clone $model->student;
        $studentId = $model->studentId;
        if (Yii::$app->request->isPost) {
            if ($model->course->program->isPrivate()) {
                $model->deleteWithTransactionalData();
                $student->setStatus();
                $response = [
                    'status' => true,
                    'url' => Url::to(['student/view', 'id' => $studentId])
                ];
            } else {
                $response = [
                    'status' => false
                ];
            }
        } else {
            $startDate = Carbon::parse($model->course->startDate);
            $endDate = Carbon::parse($model->course->endDate);
            $objects = ['Lessons', 'Payment Cycles', 'PFIs', 'Invoices'];
            $results = [];
            $dateRange = $startDate->format('M d, Y') . ' - ' . $endDate->format('M d, Y');
            foreach ($objects as $value) {
                $results[] = [
                    'objects' => $value,
                    'action' => 'will be deleted',
                    'date_range' => 'within ' . $dateRange
                ]; 
            }
            $previewDataProvider = new ArrayDataProvider([
                'allModels' => $results,
                'sort' => [
                    'attributes' => ['objects', 'action', 'date_range']
                ]
            ]);
            $searchModel = new EnrolmentPaymentSearch();
            $searchModel->enrolmentId = $model->id;
            $paymentsDataProvider = $searchModel->search(Yii::$app->request->queryParams);
            $data = $this->renderAjax('_delete-preview', [
                'model' => $model,
                'paymentsDataProvider' => $paymentsDataProvider,
                'previewDataProvider' => $previewDataProvider
            ]);
            $response = [
                'status' => true,
                'data' => $data
            ];
        }
        return $response;
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
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $model = Enrolment::find()->location($locationId)->isRegular()
            ->notDeleted()
            ->andWhere(['enrolment.id' => $id])->one();
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionCancel($id)
    {
        $model = $this->findModel($id);
        $model->student->delete();
        $model->student->customer->delete();
        return $this->redirect(['index']);
    }

    public function actionEditEndDate($id)
    {
        $changedEndDate = Yii::$app->request->get('endDate');
        $model = $this->findModel($id);
        $lastLesson = $model->lastRootLesson;
        if (!$lastLesson) {
            return [
                'status' => false,
                'message' => 'There are no lessons in the enrolment so end date cannnot be adjusted.',
            ];
        }
        $lastLessonDate = Carbon::parse($lastLesson->date);
        $action = null;
        $dateRange = null;
        $previewDataProvider = null;
        if ($changedEndDate) {
            $date = Carbon::parse($changedEndDate);
            $objects = ['Lessons'];
            $results = [];
            if ($lastLessonDate > $date) {
                $dateRange = $date->format('M d, Y') . ' - ' . $lastLessonDate->format('M d, Y');
                $action = 'shrink';
                array_push($objects, 'PFIs');
                foreach ($objects as $value) {
                    $results[] = [
                        'objects' => $value,
                        'action' => 'will be deleted',
                        'date_range' => 'within ' . $dateRange
                    ]; 
                }
            } else if ($lastLessonDate < $date) {
                $dateRange = $lastLessonDate->format('M d, Y') . ' - ' . $date->format('M d, Y');
                $action = 'extend';
                foreach ($objects as $value) {
                    $results[] = [
                        'objects' => $value,
                        'action' => 'will be created',
                        'date_range' => 'within ' . $dateRange
                    ]; 
                }
            }
            $previewDataProvider = new ArrayDataProvider([
                'allModels' => $results,
                'sort' => [
                    'attributes' => ['objects', 'action', 'date_range'],
                ],
            ]);
        }
        $post = Yii::$app->request->post();
        $course = $model->course;
        $endDate = Carbon::parse($course->endDate)->format('d-m-Y');
        $course->load(Yii::$app->getRequest()->getBodyParams(), 'Course');
        if ($post) {
            if ($course->validate()) {
                $message = null;
                $course->updateAttributes([
                    'endDate' => Carbon::parse($course->endDate)->format('Y-m-d 23:59:59')
                ]);
                $newEndDate = Carbon::parse($course->endDate);
                if ($endDate !== $newEndDate) {
                    if ($lastLessonDate > $newEndDate) {
                        $model->shrink();
                    } else if ($lastLessonDate < $newEndDate) {
                        $model->extend();
                    }
                    $model->setStatus();
                    if ($message) {
                        $message = 'Enrolment end date succesfully updated!';
                    }
                }
                $response = [
                    'status' => true,
                    'message' => $message
                ];
            } else {
                $errors = ActiveForm::validate($course);
                $response = [
                    'error' => end($errors),
                    'status' => false,
                ];
        }
        } else {
            $data = $this->renderAjax('update/_form-schedule', [
                'model' => $model,
                'action' => $action,
                'dateRange' => $dateRange,
                'course' => $model->course,
                'previewDataProvider' => $previewDataProvider
            ]);
            $response = [
                'status' => true,
                'data' => $data,
            ];
        }
        return $response;
    }

    public function actionUpdatePreferredPaymentStatus($state, $paymentCycleId)
    {
        $model = PaymentCycle::find()
                ->andWhere([
                    'id' => $paymentCycleId,
                ])
                ->notDeleted()
                ->one();
        $model->isPreferredPaymentEnabled = $state;
        if ($model->save()) {
            $response = [
                'status' => true,
            ]; 
        } else {
            $response = [
                'status' => false,
                'errors' =>$model->getErrors()
            ];
        }
        return $response;
    }

    public function actionGroupEnrolmentDelete($id)
    {
        $model = $this->findModel($id);
        $status = false;
        if ($model->course->program->isGroup()) {
            foreach ($model->lessons as $lesson) {        
                if ($model->hasInvoice($lesson->id) || $model->hasPayment()) {
                  $status = true;
                  break;
                }
            }
            if (!$status) {
                $model->delete();
            } else {
                return $response = [
                    'status' => false,
                    'error' => 'Enrolment not deleted because it associated with invoice and payments.'
                ];
            }
            return $response = [
                'status' => true,
                'url' => Url::to(['enrolment/index', 'EnrolmentSearch[showAllEnrolments]' => false]),
                'message' => 'Enrolment has been deleted.'
            ];
        }
    }
}
