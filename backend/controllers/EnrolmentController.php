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
use common\models\Label;
use common\models\CourseSchedule;
use common\models\Student;
use yii\filters\ContentNegotiator;
use backend\models\search\LessonSearch;
use yii\base\Model;
use common\models\timelineEvent\TimelineEventEnrolment;
use common\models\PaymentFrequency;
use common\models\UserPhone;
use common\models\UserAddress;
use common\models\UserEmail;
use common\models\UserContact;
use common\models\UserLocation;
use common\models\User;
use common\models\UserProfile;
use Carbon\Carbon;
use common\models\discount\EnrolmentDiscount;
use backend\models\discount\MultiEnrolmentDiscount;
use backend\models\discount\PaymentFrequencyEnrolmentDiscount;
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
				'only' => ['add', 'delete', 'edit','schedule', 
                        'group', 'update','edit-end-date', 'edit-program-rate'],
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
				->isConfirmed()
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

	public function actionGroup($courseId, $studentId)
	{
		$enrolmentModel = new Enrolment();
		$enrolmentModel->courseId = $courseId;
		$enrolmentModel->studentId = $studentId;
		$enrolmentModel->paymentFrequencyId = PaymentFrequency::LENGTH_FULL;
		$enrolmentModel->isConfirmed = true;
		if($enrolmentModel->save()) {
			return [
				'status' => true,
			];
		}
	}

	public function actionEdit($id)
    {
        $model = $this->findModel($id);
        $paymentFrequencyDiscount = new PaymentFrequencyEnrolmentDiscount();
        $multipleEnrolmentDiscount = new MultiEnrolmentDiscount();
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
        $post = Yii::$app->request->post();
        if ($post) {
            $paymentFrequencyDiscount->load($post);
            $multipleEnrolmentDiscount->load($post);
            $multipleEnrolmentDiscount->save();
            $paymentFrequencyDiscount->save();
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
            foreach ($model->enrolmentProgramRates as $key => $enrolmentProgramRate) {
                $enrolmentProgramRate->load($post['EnrolmentProgramRate'][$key], '');
                $enrolmentProgramRate->save();
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
		$locationId = Yii::$app->session->get('location_id');
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
        if($user->save()){
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
			if(!empty($address->address)) {
				$userContact = $this->createUserContact($user->id, $address->labelId);
				$address->userContactId = $userContact->id;
				$address->save();
			}
			if(!empty($phoneNumber->number)) {
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
			if($course->save()) {
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
					$paymentFrequencyDiscount->discountType = EnrolmentDiscount::VALUE_TYPE_DOLOR;
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
        $model = $this->findModel($id);
		$data = $this->renderAjax('/student/enrolment/_form-update', [
			'course' => $model->course,	
			'courseSchedule' => $model->course->courseSchedule,
			'model' => $model,
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
			$endDate = new \DateTime($course->endDate);
			$startDate		 = new \DateTime($course->startDate);
			Lesson::deleteAll([
				'courseId' => $model->course->id,
				'isConfirmed' => false,
			]);
			$lessons		 = Lesson::find()
				->where([
					'courseId' => $model->course->id,
					'status' => Lesson::STATUS_SCHEDULED,
					'type' => Lesson::TYPE_REGULAR
				])
				->joinWith('bulkRescheduleLesson')
				->isConfirmed()
				->between($startDate, $endDate)
				->all();
			$dayList = Course::getWeekdaysList();
			$courseDay = $dayList[$courseSchedule->day];
			$day = $startDate->format('l');
			if ($day !== $courseDay) {
				$startDate		 = new \DateTime($course->startDate);
				$startDate->modify('next '.$courseDay);
			}
			$teacherId = $course->teacherId;
			$course->generateLessons($lessons, $startDate, $teacherId);
			$rescheduleBeginDate = (new \DateTime($course->startDate))->format('d-m-Y');
			$rescheduleEndDate = (new \DateTime($course->endDate))->format('d-m-Y');
			return $this->redirect(['/lesson/review', 'courseId' => $course->id, 'LessonSearch[showAllReviewLessons]' => false, 'Course[startDate]' => $rescheduleBeginDate, 'Course[endDate]' => $rescheduleEndDate]);
		} else {
			return $response;
		}


    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
		if ($model->course->program->isPrivate() && $model->canDeleted()) {
            $lessons = Lesson::find()
				->where(['courseId' => $model->courseId])
				->all();
            foreach ($lessons as $lesson) {
                $lesson->delete();
            }
			$model->delete();
			return [
				'status' => true,
			];
        } else {
			return [
				'status' => false,
			];
		}
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
            if ($endDate !== $course->endDate) {
                $courseEndDate = Carbon::parse($course->endDate)->format('Y-m-d');
                $course->updateAttributes([
                    'endDate' => Carbon::parse($course->endDate)->format('Y-m-d H:i:s')
                ]);
                $startDate = null;
                $invoice = $model->addCreditInvoice($startDate, $course->endDate);
                if (!$invoice) {
                    $credit = 0;
                } else {
                    $credit = abs($invoice->invoiceBalance);
                }
                $message = '$' . $credit . ' has been credited to ' . $model->customer->publicIdentity . ' account.';
            }
            $response = [
                'status' => true,
                'message' => $message,
            ];
            return $response;
        } else {

            return [
                'status' => true,
                'data' => $data,
            ];
        }
    }
}
