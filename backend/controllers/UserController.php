<?php

namespace backend\controllers;

use Yii;
use common\models\User;
use common\models\TeacherAvailability;
use common\models\Qualification;
use common\models\Enrolment;
use backend\models\UserForm;
use common\models\Lesson;
use backend\models\search\LessonSearch;
use common\models\Note;
use common\models\Location;
use common\models\Invoice;
use backend\models\UserImportForm;
use backend\models\search\InvoiceSearch;
use common\models\TeacherUnavailability;
use backend\models\search\UserSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use common\models\Student;
use common\models\UserContact;
use common\models\LocationAvailability;
use common\models\InvoiceLineItem;
use yii\helpers\Url;
use common\models\UserEmail;
use common\models\Label;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\log\LogHistory;
use Intervention\Image\ImageManagerStatic;
use trntv\filekit\actions\DeleteAction;
use trntv\filekit\actions\UploadAction;
use common\components\controllers\BaseController;
use yii\filters\AccessControl;
use common\models\Payment;
use common\models\Transaction;
use common\models\CustomerReferralSource;
use common\models\GroupLesson;
use common\models\CustomerRecurringPayment;
/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends BaseController
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
            [
                'class' => 'yii\filters\ContentNegotiator',
                'only' => [
                    'edit-profile', 'edit-phone', 'edit-address', 'edit-email', 'edit-lesson',
                    'update-primary-email', 'delete', 'create', 'upload'
                ],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view', 'edit-profile', 'import', 'delete-contact', 
                            'create', 'edit-lesson', 'delete', 'avatar-upload'
                        ],
                        'roles' => [
                            'manageTeachers', 'manageCustomers', 'manageAdmin', 'manageStaff',
                            'manageOwners'
                        ],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['upload'],
                        'roles' => ['manageImport'],
                    ],
                ],
            ], 
        ];
    }

    public function actions()
    {
        return [
            'upload' => [
                'class' => 'common\actions\UserImportUploadAction',
                'multiple' => false,
                'disableCsrf' => true,
                'responseFormat' => Response::FORMAT_JSON,
                'responsePathParam' => 'path',
                'responseBaseUrlParam' => 'base_url',
                'responseUrlParam' => 'url',
                'responseDeleteUrlParam' => 'delete_url',
                'responseMimeTypeParam' => 'type',
                'responseNameParam' => 'name',
                'responseSizeParam' => 'size',
                'deleteRoute' => 'delete',
                'fileStorage' => 'fileStorage', // Yii::$app->get('fileStorage')
                'fileStorageParam' => 'fileStorage', // ?fileStorage=someStorageComponent
                'sessionKey' => '_uploadedFiles',
                'allowChangeFilestorage' => false,
                'validationRules' => [
                ],
                'on afterSave' => function ($event) {
                    /* @var $file \League\Flysystem\File */
                    // do something (resize, add watermark etc)
                },
            ],
            'avatar-upload' => [
                'class' => UploadAction::className(),
                'deleteRoute' => 'avatar-delete',
                'on afterSave' => function ($event) {
                    /* @var $file \League\Flysystem\File */
                    $file = $event->file;
                    $img = ImageManagerStatic::make($file->read())->fit(215, 215);
                    $file->put($img->encode());
                }
            ],
            'avatar-delete' => [
                'class' => DeleteAction::className()
            ],
        ];
    }

    /**
     * Lists all User models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    protected function getStudentDataProvider($id)
    {
        $query = Student::find()
            ->notDeleted()
            ->andWhere(['customer_id' => $id]);

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    protected function getTeacherDataProvider($id)
    {
        $query = TeacherAvailability::find()
                ->notDeleted()
                ->joinWith('userLocation')
                ->andWhere(['user_id' => $id]);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    protected function getLessonDataProvider($id, $locationId)
    {
        $lessonQuery = Lesson::find()
                ->location($locationId)
                ->customer($id)
                ->scheduledOrRescheduled()
                ->isConfirmed()
		        ->orderBy(['lesson.dueDate' => SORT_ASC, 'lesson.date' => SORT_ASC])
                ->notDeleted()
                ->notCompleted();

        return new ActiveDataProvider([
            'query' => $lessonQuery,
        ]);
    }

    protected function getEnrolledStudentDataProvider($id, $locationId)
    {
        $query = Student::find()->notDeleted()
                ->teacherStudents($locationId, $id);

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    protected function getLocationDataProvider($id)
    {
        $query = Location::find()
                ->notDeleted()
                ->joinWith('userLocations')
                ->andWhere(['user_id' => $id]);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    protected function getEnrolmentDataProvider($id)
    {
        $currentdate = new \DateTime();
        $currentDate = $currentdate->format('Y-m-d');
        $enrolmentQuery = Enrolment::find()
            ->joinWith(['student' => function ($query) use ($id) {
                $query->andWhere(['customer_id' => $id]);
            }])
            ->notDeleted()
            ->isConfirmed()
            ->isRegular()
            ->groupBy(['enrolment.id'])
            ->active();

        return new ActiveDataProvider([
            'query' => $enrolmentQuery,
        ]);
    }

    protected function getInvoiceDataProvider($model, $locationId)
    {
        $request = Yii::$app->request;
        $invoiceQuery = Invoice::find()
                ->andWhere([
                    'invoice.user_id' => $model->id,
                    'invoice.type' => Invoice::TYPE_INVOICE,
                    'invoice.location_id' => $locationId,
                ])
		->limit(10)
                ->notDeleted();
        return new ActiveDataProvider([
            'query' => $invoiceQuery,
            'sort' => ['defaultOrder' => ['date' => SORT_DESC]],
            'pagination' => false,
        ]);
    }
    
    protected function getInvoiceCount($model, $locationId) {
	    $invoiceCount = Invoice::find()
                ->andWhere([
                    'invoice.user_id' => $model->id,
                    'invoice.type' => Invoice::TYPE_INVOICE,
                    'invoice.location_id' => $locationId,
                ])
                ->notDeleted()
		->count();
	    return $invoiceCount;
    }

    protected function getPfiDataProvider($id, $locationId)
    {
        $proFormaInvoiceQuery = Invoice::find()
            ->andWhere([
                'invoice.user_id' => $id,
                'invoice.type' => Invoice::TYPE_PRO_FORMA_INVOICE,
                'invoice.location_id' => $locationId,
            ])
            ->notDeleted();
        return new ActiveDataProvider([
            'query' => $proFormaInvoiceQuery,
        ]);
    }

    protected function getUnscheduleLessonDataProvider($id)
    {
        $searchModel = new UserSearch();
        $searchModel->showAll = false;
        $searchModel->load(Yii::$app->request->get());
        $unscheduledLessons = Lesson::find()
            ->isConfirmed()
            ->joinWith(['privateLesson'])
            ->orderBy(['private_lesson.expiryDate' => SORT_ASC])
            ->andWhere(['lesson.teacherId' => $id])
            ->unscheduled()
            ->notDeleted()
            ->groupBy(['lesson.id','private_lesson.id']);
            if (!$searchModel->showAll) {
                $unscheduledLessons->notExpired(); 
            } 

        return new ActiveDataProvider([
            'query' => $unscheduledLessons,
        ]);
    }

    protected function getPaymentDataProvider($id)
    {
        return new ActiveDataProvider([
            'query' => Payment::find()
                ->andWhere(['user_id' => $id])
                ->notDeleted(),
        ]);
    }

    protected function getUnavailabilityDataProvider($id)
    {
        $unavailabilities = TeacherUnavailability::find()
            ->andWhere(['teacherId' => $id]);

        return new ActiveDataProvider([
            'query' => $unavailabilities,
        ]);
    }

    protected function getTeacherLessonDataProvider($id, $locationId)
    {
        $request = Yii::$app->request;
        $lessonSearch = new LessonSearch();
        $lessonSearch->fromDate = new \DateTime();
        $lessonSearch->toDate = new \DateTime();
        $lessonSearchModel = $request->get('LessonSearch');
        
        if (!empty($lessonSearchModel)) {
            $lessonSearch->dateRange = $lessonSearchModel['dateRange'];
            list($lessonSearch->fromDate, $lessonSearch->toDate) = explode(' - ', $lessonSearch->dateRange);
            $lessonSearch->fromDate = new \DateTime($lessonSearch['fromDate']);
            $lessonSearch->toDate = new \DateTime($lessonSearch['toDate']);
            $lessonSearch->summariseReport=$lessonSearchModel['summariseReport'];
        }
        $teacherLessons = Lesson::find()
            ->innerJoinWith('enrolment')
            ->location($locationId)
            ->andWhere(['lesson.teacherId' => $id])
            ->notDeleted()
            ->scheduledOrRescheduled()
            ->isConfirmed()
            ->between($lessonSearch->fromDate, $lessonSearch->toDate)
            ->orderBy(['date' => SORT_ASC]);
			if($lessonSearch->summariseReport) {
				$teacherLessons->groupBy(['DATE(lesson.date)']);
			} 
        return new ActiveDataProvider([
            'query' => $teacherLessons,
            'pagination' => false,
        ]);
    }

    protected function getOpeningBalanceCredit($id)
    {
        return Invoice::find()
            ->openingBalance()
            ->customer($id)
            ->andWhere(['<', 'invoice.balance', 0])
            ->notDeleted()
            ->one();
    }

    protected function getPositiveOpeningBalance($id)
    {
        return Invoice::find()
            ->openingBalance()
            ->customer($id)
            ->andWhere(['>', 'invoice.balance', 0])
            ->notDeleted()
            ->one();
    }

    protected function getNoteDataProvider($id)
    {
        $notes = Note::find()
            ->andWhere(['instanceId' => $id, 'instanceType' => Note::INSTANCE_TYPE_USER])
            ->orderBy(['createdOn' => SORT_DESC]);

        return new ActiveDataProvider([
            'query' => $notes,
        ]);
    }
    
    protected function getAccountDataProvider($id)
    {
        $paymentQuery = Transaction::find()
                ->manualPayments($id);
        $invoiceQuery = Transaction::find()
                ->invoices($id)
                ->union($paymentQuery)
                ->all();
        
        $ids = ArrayHelper::getColumn($invoiceQuery, 'id');
        $accountQuery = Transaction::find()
            ->andWhere(['id' => $ids])
            ->orderBy(['transaction.id' => SORT_DESC]);
        return new ActiveDataProvider([
            'query' => $accountQuery
        ]);
    }

    protected function getPrivateQualificationDataProvider($id)
    {
        $privatePrograms = Qualification::find()
            ->joinWith(['program' => function ($query) {
                $query->privateProgram();
            }])
            ->andWhere(['teacher_id' => $id]);

        return new ActiveDataProvider([
            'query' => $privatePrograms,
        ]);
    }

    protected function getGroupQualificationDataProvider($id)
    {
        $groupPrograms = Qualification::find()
            ->joinWith(['program' => function ($query) {
                $query->group();
            }])
            ->andWhere(['teacher_id' => $id]);

        return new ActiveDataProvider([
            'query' => $groupPrograms,
        ]);
    }

    protected function getTimeVoucherDataProvider($id, $fromDate, $toDate, $summariseReport)
    {
        $timeVoucher = InvoiceLineItem::find()
            ->notDeleted()
            ->joinWith(['invoice' => function ($query) use ($fromDate,$toDate) {
                $query->andWhere(['invoice.isDeleted' => false, 'invoice.type' => Invoice::TYPE_INVOICE])
                    ->between((new \DateTime($fromDate))->format('Y-m-d'), (new \DateTime($toDate))->format('Y-m-d'));
            }])
            ->joinWith(['lesson' => function ($query) use ($id) {
                $query->andWhere(['lesson.teacherId' => $id])
                ->groupBy('lesson.id');
            }])
           ->orderBy(['invoice.date' => SORT_ASC]);
        if($summariseReport) { 
            $timeVoucher->groupBy('invoice.date');
        }    
        return new ActiveDataProvider([
            'query' => $timeVoucher,
            'pagination' => false,
        ]);
    }

    protected function getLogDataProvider($id)
    {
        return new ActiveDataProvider([
            'query' => LogHistory::find()
                ->user($id)]);
    }

    protected function getTeacherAvailabilities($id, $locationId)
    {
        return TeacherAvailability::find()
            ->joinWith(['userLocation' => function ($query) use ($locationId, $id) {
                $query->andWhere(['user_location.location_id' => $locationId, 'user_id' => $id]);
            }])
            ->groupBy(['teacher_availability_day.id','day'])
            ->notDeleted()
            ->all();
    }
        
    /**
     * Displays a single User model.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $request = Yii::$app->request;
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $minLocationAvailability = LocationAvailability::find()
            ->notDeleted()
            ->location($locationId)
            ->locationaAvailabilityHours()
            ->orderBy(['fromTime' => SORT_ASC])
            ->one();
        $maxLocationAvailability = LocationAvailability::find()
            ->notDeleted()
            ->location($locationId)
            ->locationaAvailabilityHours()
            ->orderBy(['toTime' => SORT_DESC])
            ->one();
        if (empty($minLocationAvailability)) {
            $minTime = LocationAvailability::DEFAULT_FROM_TIME;
        } else {
            $minTime = (new \DateTime($minLocationAvailability->fromTime))->format('H:i:s');
        }
        if (empty($maxLocationAvailability)) {
            $maxTime = LocationAvailability::DEFAULT_TO_TIME;
        } else {
            $maxTime = (new \DateTime($maxLocationAvailability->toTime))->format('H:i:s');
        }
        $searchModel = new UserSearch();
        $searchModel->accountView = false;
        $request = Yii::$app->request;
        $db = $searchModel->search($request->queryParams);
        $lessonSearchModel=new LessonSearch();
        $lessonSearchModel->dateRange=(new\DateTime())->format('M d,Y').' - '.(new\DateTime())->format('M d,Y');
        $lessonSearchModel->load($request->get());
        $invoiceSearchModel = new InvoiceSearch();
        $invoiceSearchModel->dateRange = (new \DateTime('previous week monday'))->format('M d,Y') . ' - ' . (new \DateTime('previous week saturday'))->format('M d,Y');
        $invoiceSearchModel->load($request->get());
        list($invoiceSearchModel->fromDate, $invoiceSearchModel->toDate) = explode(' - ', $invoiceSearchModel->dateRange);
        return $this->render('view', [
            'isCustomerView' => $searchModel->accountView,
            'minTime' => $minTime,
            'maxTime' => $maxTime,
            'model' => $model,
            'student' => new Student(),
            'searchModel' => $searchModel,
            'lessonSearchModel' => $lessonSearchModel,
            'invoiceSearchModel' => $invoiceSearchModel,
            'dataProvider' => $this->getStudentDataProvider($id),
            'teacherDataProvider' => $this->getTeacherDataProvider($id),
            'lessonDataProvider' => $this->getLessonDataProvider($id, $locationId),
            'locationDataProvider' => $this->getLocationDataProvider($id),
            'enrolmentDataProvider' => $this->getEnrolmentDataProvider($id),
            'invoiceDataProvider' => $this->getInvoiceDataProvider($model, $locationId),
            'studentDataProvider' => $this->getEnrolledStudentDataProvider($id, $locationId),
            'paymentDataProvider' => $this->getPaymentDataProvider($id),
            'proFormaInvoiceDataProvider' => $this->getPfiDataProvider($id, $locationId),
            'unscheduledLessonDataProvider' => $this->getUnscheduleLessonDataProvider($id),
            'positiveOpeningBalanceModel' => $this->getPositiveOpeningBalance($id),
            'openingBalanceCredit' => $this->getOpeningBalanceCredit($id),
            'teacherLessonDataProvider' => $this->getTeacherLessonDataProvider($id, $locationId,$lessonSearchModel->summariseReport),
            'noteDataProvider' => $this->getNoteDataProvider($id),
            'teachersAvailabilities' => $this->getTeacherAvailabilities($id, $locationId),
            'privateQualificationDataProvider' => $this->getPrivateQualificationDataProvider($id),
            'groupQualificationDataProvider' => $this->getGroupQualificationDataProvider($id),
            'timeVoucherDataProvider' => $this->getTimeVoucherDataProvider($id, $invoiceSearchModel->fromDate, $invoiceSearchModel->toDate,$invoiceSearchModel->summariseReport),
            'unavailability' => $this->getUnavailabilityDataProvider($id),
            'logDataProvider' => $this->getLogDataProvider($id),
            'invoiceCount' => $this->getInvoiceCount($model, $locationId),
            'paymentsDataProvider' => $this->getPaymentsDataProvider($id),
            'paymentCount' => $this->getPaymentCount($id),
            'outstandingInvoice' => $this->getOutstandingInvoice($id),
            'customerRecurringPaymentsDataProvider' => $this->getCustomerRecurringPaymentsDataProvider($id),
            'privateLessonDueDataProvider' => $this->getPrivateLessonDueDataProvider($id, $locationId),
            'groupLessonDueDataProvider' => $this->getGroupLessonDueDataProvider($id, $locationId),
        ]);
    }

    public function actionCreate()
    {
        $model = new UserForm();
        $emailModel = new UserEmail();
        $customerReferralSource = new CustomerReferralSource();
        $model->roles = Yii::$app->request->queryParams['role_name'];
        if ($model->roles !== User::ROLE_STAFFMEMBER) {
            $canLogin = true;
        } else {
            $canLogin = false;
        }
        if ($model->roles == User::ROLE_ADMINISTRATOR || $model->roles == User::ROLE_OWNER) {
            $emailModel->setScenario(UserEmail::SCENARIO_USER_CREATE);
        }
        $model->canLogin = $canLogin;
        $data = $this->renderAjax('_form', [
            'model' => $model,
            'emailModel' => $emailModel,
            'customerReferralSource' => $customerReferralSource,
        ]);
        $model->locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $request = Yii::$app->request;
        if ($request->post()) {
            if ($model->load($request->post()) && $emailModel->load($request->post())) {
                $model->save();
               if ($customerReferralSource->load($request->post())) {
                   if($customerReferralSource->referralSourceId) {
                   $customerReferralSource->userId = $model->getModel()->id;
                   $customerReferralSource->save();
                   }
               }
                if (!empty($emailModel->email)) {
                    $userContact = new UserContact();
                    $userContact->userId = $model->getModel()->id;
                    $userContact->labelId = Label::LABEL_WORK;
                    $userContact->isPrimary = true;
                    $userContact->save();
                    $emailModel->labelId = $userContact->labelId;
                    $emailModel->userContactId = $userContact->id;
                    $emailModel->save();
                }
                $response = [
                    'status' => true,
                    'url' => Url::to(['view', 'UserSearch[role_name]' => $model->roles, 'id' => $model->getModel()->id])
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

    public function actionEditProfile($id)
    {
        $request = Yii::$app->request;
        $model = new UserForm();
        $model->setModel($this->findModel($id));
        $userModel = $model->getModel();
        $userProfile  = $userModel->userProfile;
        if ($userModel->customerReferralSource) {
            $customerReferralSource = $userModel->customerReferralSource;
        } else {
            $customerReferralSource = new CustomerReferralSource(); 
        }
        if ($model->load($request->post()) && $userProfile->load($request->post())) {
           
            if (!empty($model->password)) {
                $model->getModel()->setPassword($model->password);
            }
            if (!empty($model->pin)) {
                $model->getModel()->setPin($model->pin);
            }
            if ($model->save()) {  
               if ($userProfile->validate()) {
                   $userProfile->save();
                $customerReferralSource->load($request->post());
                if($customerReferralSource->referralSourceId) {
                    if (!$customerReferralSource->referralSource->isOther()) {
                        $customerReferralSource->description = null;
                    }
                $customerReferralSource->userId = $userModel->id;
                $customerReferralSource->save();
                }
                return [
                   'status' => true,
                ];
            }
            else {  
                return [
                    'status' => false,
                    'errors' => $userProfile->getErrors(),
                ];
            }
            } else {
                $errors = ActiveForm::validate($model);
                return [
                    'status' => false,
                    'errors' => $errors
                ];
            }
        }
    }
        
    /**
     * Updates an existing User model.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionImport()
    {
        $model = new UserImportForm();
        if ($model->load(Yii::$app->request->post()) && $model->import()) {
            return $this->redirect(['import']);
        }

        return $this->render('import', [
                    'model' => $model,
        ]);
    }
    
    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param int $id
     *
     * @return User the loaded model
     *
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
        $lastRole = end($roles);
        $adminModel = User::findOne(['id' => $id]);
        $model = User::find()
                ->location($locationId)
                ->andWhere(['user.id' => $id])
                ->notDeleted()
                ->one();
        if ($model !== null) {
            return $model;
        } elseif ($lastRole->name === User::ROLE_ADMINISTRATOR && $adminModel != null) {
            return $adminModel;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    public function actionDelete($id)
    {
        $model =  $this->findModel($id);
        $model->setScenario(User::SCENARIO_DELETE);
        $roles = ArrayHelper::getColumn(Yii::$app->authManager->getRolesByUser($id), 'name');
        $role = end($roles);
        if ($model->validate()) {
            $model->delete();
            $response = [
                'status' => true,
                'url' => Url::to(['index', 'UserSearch[role_name]' => $role])
            ];
        } else {
            $response = [
                'status' => false,
                'message' => current($model->getErrors())
            ];
        }
        return $response;
    }
    
    protected function getPaymentsDataProvider($id)
    {
        return new ActiveDataProvider([
            'query' => Payment::find()
                ->andWhere(['user_id' => $id])
                ->notDeleted()
                ->exceptAutoPayments()
                ->limit(10),
            'pagination' => false,
            'sort' => ['defaultOrder' => ['date' => SORT_DESC]],
        ]);
    }

    protected function getCustomerRecurringPaymentsDataProvider($id)
    {
        return new ActiveDataProvider([
            'query' => CustomerRecurringPayment::find()
                ->notDeleted()
                ->andWhere(['customerId' => $id]),
            'pagination' => false,
        ]);
    }

    protected function getPaymentCount($id) 
    {
	    $paymentCount = Payment::find()
                ->andWhere(['user_id' => $id])
                ->notDeleted()
                ->exceptAutoPayments()
		        ->count();
	    return $paymentCount;
    }

    protected function getOutstandingInvoice($id)
    {
        $outstandingInvoice = Invoice::find()
                ->customer($id)
                ->invoice()
                ->andWhere(['>', 'invoice.balance', 0.0])
                ->notDeleted();
        return new ActiveDataProvider([
            'query' => $outstandingInvoice,
            'sort' => ['defaultOrder' => ['date' => SORT_ASC]],
            'pagination' => false,
        ]);
    }

    protected function getPrivateLessonDueDataProvider($id, $locationId)
    {
        $lessonQuery = Lesson::find()
                ->location($locationId)
                ->customer($id)
                ->privatelessons()
                ->duelessons()
                ->isConfirmed()
                ->joinWith(['privateLesson' => function($query) {
                    $query->andWhere(['>', 'private_lesson.balance', 0.00]);
                }])
                ->orderBy(['lesson.dueDate' => SORT_ASC, 'lesson.date' => SORT_ASC])
                ->notCanceled()
                ->unInvoiced()
                ->notDeleted();
        return new ActiveDataProvider([
            'query' => $lessonQuery,
            'pagination' => false,
        ]);
    }

    protected function getGroupLessonDueDataProvider($id, $locationId)
    {
        $lessonQuery = GroupLesson::find()
                ->joinWith(['lesson' => function($query) use ($locationId) {
                    $query->location($locationId)
                        ->isConfirmed()
                        ->orderBy(['lesson.date' => SORT_ASC])
                        ->notCanceled()
                        ->notDeleted();
                }])
                ->joinWith(['enrolment' => function($query) use ($id) {
                    $query->notDeleted()
                        ->isConfirmed()
                        ->customer($id);
                }])
                ->andWhere(['>', 'group_lesson.balance', 0.00])
                ->orderBy(['group_lesson.dueDate' => SORT_ASC])
                ->dueLessons();
        return new ActiveDataProvider([
            'query' => $lessonQuery,
            'pagination' => false,
        ]);
    }
}
