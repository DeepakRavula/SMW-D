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
use common\models\CourseSchedule;
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
use common\models\discount\EnrolmentDiscount;
use backend\models\discount\MultiEnrolmentDiscount;
use backend\models\discount\PaymentFrequencyEnrolmentDiscount;
use common\models\log\StudentLog;
use common\models\log\DiscountLog;
use yii\widgets\ActiveForm;
use common\components\controllers\BaseController;
use yii\filters\AccessControl;

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
                'only' => ['add', 'delete', 'edit', 'schedule', 'group', 'update',
                    'edit-end-date', 'edit-program-rate', 'reschedule'
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
                            'reschedule'
                        ],
                        'roles' => ['manageEnrolments'],
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
        $lessonDataProvider = new ActiveDataProvider([
            'query' => Lesson::find()
                ->andWhere(['courseId' => $model->course->id])
                ->scheduledOrRescheduled()
                ->isConfirmed()
                ->notDeleted()
                ->orderBy(['lesson.date' => SORT_ASC]),
            'pagination' => false,
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
            'lessonDataProvider' => $lessonDataProvider,
            'paymentCycleDataProvider' => $paymentCycleDataProvider,
            'logDataProvider' => $logDataProvider,
        ]);
    }

    public function actionGroup($courseId, $studentId)
    {
        $course = Course::findOne($courseId);
        if ($course->hasExtraCourse()) {
            foreach ($course->extraCourses as $extraCourse) {
                $extraCourse->studentId = $studentId;
                $enrolment = $extraCourse->createExtraLessonEnrolment();
                $enrolment->createProFormaInvoice();
            }
        }
        $enrolmentModel = new Enrolment();
        $enrolmentModel->courseId = $courseId;
        $enrolmentModel->studentId = $studentId;
        $enrolmentModel->paymentFrequencyId = PaymentFrequency::LENGTH_FULL;
        $enrolmentModel->isConfirmed = true;
        if ($enrolmentModel->save()) {
            $loggedUser = User::findOne(['id' => Yii::$app->user->id]);
            $enrolmentModel->on(
                Enrolment::EVENT_AFTER_INSERT,
                [new StudentLog(), 'addGroupEnrolment'],
                ['loggedUser' => $loggedUser]
            );
            $enrolmentModel->trigger(Enrolment::EVENT_AFTER_INSERT);
            $invoice = $enrolmentModel->createProFormaInvoice();
            return [
                'status' => true,
                'url' => Url::to(['/invoice/view', 'id' => $invoice->id])
            ];
        }
    }

    public function actionEdit($id)
    {
        $model = $this->findModel($id);
        $paymentFrequencyDiscount = new PaymentFrequencyEnrolmentDiscount();
        $multipleEnrolmentDiscount = new MultiEnrolmentDiscount();
        $oldMultipleEnrolmentDiscount = $model->getMultipleEnrolmentDiscountValue();

        if ($model->hasMultiEnrolmentDiscount()) {
            $multipleEnrolmentDiscount = $multipleEnrolmentDiscount->setModel($model->multipleEnrolmentDiscount);
        }
        if ($model->hasPaymentFrequencyDiscount()) {
            $paymentFrequencyDiscount = $paymentFrequencyDiscount->setModel($model->paymentFrequencyDiscount);
        }
        $paymentFrequencyDiscount->enrolmentId = $id;
        $multipleEnrolmentDiscount->enrolmentId = $id;
        $data = $this->renderAjax('update/_form', [
            'model' => $model,
            'multipleEnrolmentDiscount' => $multipleEnrolmentDiscount,
            'paymentFrequencyDiscount' => $paymentFrequencyDiscount,
        ]);
        $oldPaymentFrequency = $model->paymentFrequencyId;
        $oldPaymentFrequencyDiscount  = $model->getPaymentFrequencyDiscountValue();
        $post = Yii::$app->request->post();
        if ($post) {
            $paymentFrequencyDiscount->load($post);
            $multipleEnrolmentDiscount->load($post);
            $multipleEnrolmentDiscount->save();
            $loggedUser                   = User::findOne(['id' => Yii::$app->user->id]);
            $paymentFrequencyDiscount->save();
            if ((int) $oldMultipleEnrolmentDiscount != (int) $multipleEnrolmentDiscount->discount) {
                $model->on(
                    Enrolment::EVENT_AFTER_UPDATE,
                    [new DiscountLog(), 'enrolmentMultipleDiscountEdit'],
                    ['loggedUser' => $loggedUser, 'oldDiscount' => $oldMultipleEnrolmentDiscount,'newDiscount' => $multipleEnrolmentDiscount->discount]
                );
            }
            if ((int) $oldPaymentFrequencyDiscount != (int) $paymentFrequencyDiscount->discount) {
                $model->on(
                   Enrolment::EVENT_AFTER_UPDATE,
                    [new DiscountLog(), 'enrolmentPaymentFrequencyDiscountEdit'],
                    ['loggedUser' => $loggedUser, 'oldDiscount' => $oldPaymentFrequencyDiscount,'newDiscount'=>$paymentFrequencyDiscount->discount]
               );
            }
            if ($model->load($post) && $model->save()) {
                if ((int) $oldPaymentFrequency !== (int) $model->paymentFrequencyId) {
                    $model->resetPaymentCycle();
                }
            }
           
            
            $message = '';
            return [
                'status' => true,
                'message' => $message,
            ];
        } else {
            return [
                'status' => true,
                'data' => $data,
            ];
        }
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
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $request = Yii::$app->request;
        $course = new Course();
        $courseSchedule = new CourseSchedule();
        $user = new User();
        $userProfile = new UserProfile();
        $phoneNumber = new UserPhone();
        $address = new UserAddress();
        $userEmail = new UserEmail();
        $userLocation = new UserLocation();
        $student = new Student();
        $multipleEnrolmentDiscount = new EnrolmentDiscount();
        $paymentFrequencyDiscount = new EnrolmentDiscount();
            
        $post = $request->post();
        $course->load(Yii::$app->getRequest()->getBodyParams(), 'Course');
        $user->load(Yii::$app->getRequest()->getBodyParams(), 'User');
        $userProfile->load(Yii::$app->getRequest()->getBodyParams(), 'UserProfile');
        $phoneNumber->load(Yii::$app->getRequest()->getBodyParams(), 'UserPhone');
        $address->load(Yii::$app->getRequest()->getBodyParams(), 'UserAddress');
        $userEmail->load(Yii::$app->getRequest()->getBodyParams(), 'UserEmail');
        $student->load(Yii::$app->getRequest()->getBodyParams(), 'Student');
        $courseSchedule->load(Yii::$app->getRequest()->getBodyParams(), 'CourseSchedule');
        $paymentFrequencyDiscount->load($post['PaymentFrequencyDiscount'], '');
        $multipleEnrolmentDiscount->load($post['MultipleEnrolmentDiscount'], '');
        $user->status = User::STATUS_DRAFT;
	$user->canLogin=true;
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
            $student->status = Student::STATUS_DRAFT;
            $student->save();

            //save course
            $dayList = Course::getWeekdaysList();
            $course->locationId = $locationId;
            $courseSchedule->day = array_search($courseSchedule->day, $dayList);
            $courseSchedule->studentId = $student->id;
            if ($course->save()) {
                $courseSchedule->courseId = $course->id;
                $courseSchedule->save();
                    
                if (!empty($multipleEnrolmentDiscount->discount)) {
                    $multipleEnrolmentDiscount->enrolmentId = $course->enrolment->id;
                    $multipleEnrolmentDiscount->discountType = EnrolmentDiscount::VALUE_TYPE_PERCENTAGE;
                    $multipleEnrolmentDiscount->type = EnrolmentDiscount::TYPE_MULTIPLE_ENROLMENT;
                    $multipleEnrolmentDiscount->save();
                }
                if (!empty($paymentFrequencyDiscount->discount)) {
                    $paymentFrequencyDiscount->enrolmentId = $course->enrolment->id;
                    $paymentFrequencyDiscount->discountType = EnrolmentDiscount::VALUE_TYPE_DOLLAR;
                    $paymentFrequencyDiscount->type = EnrolmentDiscount::TYPE_PAYMENT_FREQUENCY;
                    $paymentFrequencyDiscount->save();
                }
            }
            return $this->redirect(['lesson/review', 'courseId' => $course->id, 'LessonSearch[showAllReviewLessons]' => false, 'Enrolment[type]' => Enrolment::TYPE_REVERSE]);
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
                    'courseSchedule' => $model->course->courseSchedule,
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
            list($fromDate, $toDate) = explode(' - ', $courseReschedule->dateRangeToChangeSchedule);
            $startDate = new \DateTime($fromDate);
            $endDate = new \DateTime($toDate);
            if ($courseReschedule->validate()) {
                $courseReschedule->reschdeule();
                $rescheduleBeginDate = $startDate->format('d-m-Y');
                $rescheduleEndDate = $endDate->format('d-m-Y');
                $url = Url::to(['/lesson/review', 'courseId' => $course->id,
                    'LessonSearch[showAllReviewLessons]' => false, 'Course[startDate]' => $rescheduleBeginDate,
                    'Course[endDate]' => $rescheduleEndDate]);
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
            $invoice = $model->addLessonsCredit($lessons);
            if ($invoice) {
                $message = '$' . $invoice->balance . ' has been credited to ' . $model->customer->publicIdentity . ' account.';
            }
            $model->deleteWithOutTransactionalData();
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
            ->andWhere(['enrolment.id' => $id, 'isDeleted' => false])->one();
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionEditEndDate($id)
    {
        $model = $this->findModel($id);
        $data = $this->renderAjax('update/_form-schedule', [
            'model' => $model,
            'course' => $model->course
        ]);
        $post = Yii::$app->request->post();
        $course = $model->course;
        $endDate = Carbon::parse($course->endDate)->format('d-m-Y');
        $course->load(Yii::$app->getRequest()->getBodyParams(), 'Course');
        if ($post) {
            $message = null;
            $course->updateAttributes([
                'endDate' => Carbon::parse($course->endDate)->format('Y-m-d 23:59:59')
            ]);
            $newEndDate = Carbon::parse($course->endDate);
            if ($endDate !== $newEndDate) {
                $lastLesson = $model->lastRootLesson;
                $lastLessonDate = Carbon::parse($lastLesson->date);
                if ($lastLessonDate > $newEndDate) {
                    $invoice = $model->shrink();
                    if (!$invoice) {
                        $credit = 0;
                    } else {
                        $credit = abs($invoice->invoiceBalance);
                        $message = '$' . $credit . ' has been credited to ' . $model->customer->publicIdentity . ' account.';
                    }
                } else if ($lastLessonDate < $newEndDate) {
                    $model->extend();
                }
                if ($message) {
                    $message = 'Enrolment end date succesfully updated!';
                }
            }
            $response = [
                'status' => true,
                'message' => $message
            ];
        } else {
            $response = [
                'status' => true,
                'data' => $data,
            ];
        }
        return $response;
    }
}
