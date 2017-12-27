<?php

namespace backend\controllers;

use Yii;
use common\models\User;
use common\models\CompanyAccount;
use common\models\TeacherAvailability;
use common\models\Qualification;
use common\models\Enrolment;
use backend\models\UserForm;
use common\models\Lesson;
use common\models\CustomerAccount;
use backend\models\search\LessonSearch;
use common\models\Note;
use common\models\Location;
use common\models\Invoice;
use backend\models\UserImportForm;
use backend\models\search\InvoiceSearch;
use common\models\TeacherUnavailability;
use backend\models\search\UserSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\base\Model;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use common\models\Student;
use common\models\Program;
use common\models\UserContact;
use common\models\LocationAvailability;
use common\models\InvoiceLineItem;
use yii\helpers\Url;
use common\models\UserEmail;
use common\models\Label;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;
use common\models\Payment;
use common\models\UserAddress;
use Intervention\Image\ImageManagerStatic;
use trntv\filekit\actions\DeleteAction;
use trntv\filekit\actions\UploadAction;
/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends \common\components\backend\BackendController
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
                'only' => ['edit-profile', 'edit-phone', 'edit-address', 'edit-email', 'edit-lesson', 'update-primary-email', 'delete'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
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
            ->andWhere(['customer_id' => $id])
			->active();
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }
	protected function  getTeacherDataProvider($id)
	{
		$query = TeacherAvailability::find()
                ->joinWith('userLocation')
                ->where(['user_id' => $id]);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
	}
	protected function  getLessonDataProvider($id, $locationId)
	{
		
		$lessonQuery = Lesson::find()
                ->location($locationId)
                ->student($id)
                ->where(['lesson.status' => [Lesson::STATUS_SCHEDULED, Lesson::STATUS_COMPLETED]])
				->isConfirmed()
                ->notDeleted();

        return new ActiveDataProvider([
            'query' => $lessonQuery,
        ]);
	}
	protected function  getEnrolledStudentDataProvider($id, $locationId)
	{
		$query = Student::find()->notDeleted()
                ->teacherStudents($locationId, $id)
				->active();

        return new ActiveDataProvider([
            'query' => $query,
        ]);
	}
	protected function getLocationDataProvider($id)
	{
		$query = Location::find()
                ->joinWith('userLocations')
                ->where(['user_id' => $id]);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
	}
	protected function getEnrolmentDataProvider($id, $locationId)
	{
		$enrolmentQuery = Enrolment::find()
            ->location($locationId)
            ->joinWith(['student' => function ($query) use ($id) {
                $query->where(['customer_id' => $id])
                ->active();
            }])
            ->notDeleted()
            ->isConfirmed()
            ->isRegular();

        return new ActiveDataProvider([
            'query' => $enrolmentQuery,
        ]);
	}
	protected function getInvoiceDataProvider($model, $locationId)
	{
		$request = Yii::$app->request;
		$currentDate = new \DateTime();
        $model->fromDate = $currentDate->format('M d,Y');
        $model->toDate = $currentDate->format('M d,Y');
        $model->dateRange = $model->fromDate.' - '.$model->toDate;
        $userRequest = $request->get('User');
		if(!empty($userRequest)) {
			list($model->fromDate, $model->toDate) = explode(' - ', $userRequest['dateRange']);
			$invoiceStatus = $userRequest['invoiceStatus'];
			$studentId = $userRequest['studentId'];
		} 
		$fromDate =  (new \DateTime($model->fromDate))->format('Y-m-d');
        $toDate =(new \DateTime($model->toDate))->format('Y-m-d');
        $invoiceQuery = Invoice::find()
                ->andWhere([
					'invoice.user_id' => $model->id,
                    'invoice.type' => Invoice::TYPE_INVOICE,
                    'invoice.location_id' => $locationId,
                ])
				->notDeleted()
				->between($fromDate,$toDate);
		if(!empty($invoiceStatus) && (int)$invoiceStatus !== UserSearch::STATUS_ALL) {
			$invoiceQuery->andWhere(['invoice.status' => $invoiceStatus]);
		}
		if(!empty($studentId)) {
			$invoiceQuery->student($studentId);
		}

        return new ActiveDataProvider([
            'query' => $invoiceQuery,
        ]);
	}
	protected function getPfiDataProvider($id, $locationId)
	{
		$proFormaInvoiceQuery = Invoice::find()
			->where([
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
		$unscheduledLessons = Lesson::find()
			->enrolled()
			->isConfirmed()
            ->joinWith(['privateLesson'])
            ->orderBy(['private_lesson.expiryDate' => SORT_DESC])
			->andWhere(['lesson.teacherId' => $id])
			->unscheduled()
			->notDeleted()
            ->groupBy('id');

        return new ActiveDataProvider([
            'query' => $unscheduledLessons,
        ]);
	}
	protected function getPaymentDataProvider($id)
	{
		return new ActiveDataProvider([
            'query' => payment::find()
                ->where(['user_id' => $id]),
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
		
		if(!empty($lessonSearchModel)) {
            $lessonSearch->dateRange = $lessonSearchModel['dateRange'];
            list($lessonSearch->fromDate, $lessonSearch->toDate) = explode(' - ', $lessonSearch->dateRange);
			$lessonSearch->fromDate = new \DateTime($lessonSearch['fromDate']);
			$lessonSearch->toDate = new \DateTime($lessonSearch['toDate']);
		}
		$teacherLessons = Lesson::find()
			->innerJoinWith('enrolment')
			->location($locationId)
			->where(['lesson.teacherId' => $id])
			->notDeleted()
			->andWhere(['status' => [Lesson::STATUS_COMPLETED, Lesson::STATUS_SCHEDULED]])
			->isConfirmed()
			->between($lessonSearch->fromDate, $lessonSearch->toDate)
			->orderBy(['date' => SORT_ASC]);
			
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
			->where(['instanceId' => $id, 'instanceType' => Note::INSTANCE_TYPE_USER])
			->orderBy(['createdOn' => SORT_DESC]);

        return new ActiveDataProvider([
            'query' => $notes,
        ]);
	}
	protected function getAccountDataProvider($id, $accountView)
	{
            if (!$accountView) {
                $accountQuery = CompanyAccount::find()
                        ->where(['userId' => $id])
                        ->orderBy(['transactionId' => SORT_DESC]);
            } else {
                $accountQuery = CustomerAccount::find()
                        ->where(['userId' => $id])
                        ->orderBy(['transactionId' => SORT_DESC]);
            }
            return new ActiveDataProvider([
                'query' => $accountQuery
            ]);
	}
	protected function getPrivateQualificationDataProvider($id)
	{
		$privatePrograms = Qualification::find()
			->joinWith(['program' => function($query) {
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
			->joinWith(['program' => function($query) {
				$query->group();
			}])
			->andWhere(['teacher_id' => $id]);

		return new ActiveDataProvider([
            'query' => $groupPrograms,
        ]);	
	}
	protected function getTimeVoucherDataProvider($id,$fromDate,$toDate,$summariseReport)
	{
				$timeVoucher = InvoiceLineItem::find()
                                        ->notDeleted()
			->joinWith(['invoice' => function($query)use($fromDate,$toDate){
				$query->andWhere(['invoice.isDeleted' => false, 'invoice.type' => Invoice::TYPE_INVOICE])
					->between((new \DateTime($fromDate))->format('Y-m-d'), (new \DateTime($toDate))->format('Y-m-d'));
			}])
			->joinWith(['lesson' => function($query) use($id){
				$query->andWhere(['lesson.teacherId' => $id]);
			}]);
			if($summariseReport) {
				$timeVoucher->groupBy('DATE(invoice.date)');	
			} else {
				$timeVoucher->orderBy(['invoice.date' => SORT_ASC]);
			}
			
		return new ActiveDataProvider([
			'query' => $timeVoucher,
			'pagination' => false,
		]);	
	}
	protected function getTeacherAvailabilities($id, $locationId)
	{
		return TeacherAvailability::find()
            ->joinWith(['userLocation' => function ($query) use ($locationId, $id) {
                $query->andWhere(['user_location.location_id' => $locationId, 'user_id' => $id]);
            }])
            ->groupBy('day')
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
        $session = Yii::$app->session;
        $request = Yii::$app->request;
        $locationId = \Yii::$app->session->get('location_id');
        $locationAvailabilityMinTime = LocationAvailability::find()
            ->where(['locationId' => $locationId])
            ->orderBy(['fromTime' => SORT_ASC])
            ->one();
        $locationAvailabilityMaxTime = LocationAvailability::find()
            ->where(['locationId' => $locationId])
            ->orderBy(['toTime' => SORT_DESC])
            ->one();
        $minTime                     = $locationAvailabilityMinTime->fromTime;
        $maxTime                     = $locationAvailabilityMaxTime->toTime;
        $searchModel = new UserSearch();
        $searchModel->accountView = false;
        $db = $searchModel->search(Yii::$app->request->queryParams);
        $lessonSearchModel=new LessonSearch();
        $lessonSearchModel->dateRange=(new\DateTime())->format('M d,Y').' - '.(new\DateTime())->format('M d,Y');
        $invoiceSearchModel = new InvoiceSearch();
        $invoiceSearchModel->dateRange = (new\DateTime())->format('M d,Y') . ' - ' . (new\DateTime())->format('M d,Y');
		$invoiceSearch = $request->get('InvoiceSearch');
		
		if(!empty($invoiceSearch)) {
            $invoiceSearchModel->dateRange = $invoiceSearch['dateRange'];
            list($invoiceSearchModel->fromDate, $invoiceSearchModel->toDate) = explode(' - ', $invoiceSearchModel->dateRange);
            $invoiceSearchModel->summariseReport = $invoiceSearch['summariseReport'];
        }

        return $this->render('view', [
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
            'enrolmentDataProvider' => $this->getEnrolmentDataProvider($id, $locationId),
            'invoiceDataProvider' => $this->getInvoiceDataProvider($model, $locationId),
            'studentDataProvider' => $this->getEnrolledStudentDataProvider($id, $locationId),
            'paymentDataProvider' => $this->getPaymentDataProvider($id),
            'proFormaInvoiceDataProvider' => $this->getPfiDataProvider($id, $locationId),
            'unscheduledLessonDataProvider' => $this->getUnscheduleLessonDataProvider($id),
            'positiveOpeningBalanceModel' => $this->getPositiveOpeningBalance($id),
            'openingBalanceCredit' => $this->getOpeningBalanceCredit($id),
            'teacherLessonDataProvider' => $this->getTeacherLessonDataProvider($id, $locationId),
            'noteDataProvider' => $this->getNoteDataProvider($id),
            'accountDataProvider' => $this->getAccountDataProvider($id, $searchModel->accountView),
            'teachersAvailabilities' => $this->getTeacherAvailabilities($id, $locationId),
			'privateQualificationDataProvider' => $this->getPrivateQualificationDataProvider($id),
			'groupQualificationDataProvider' => $this->getGroupQualificationDataProvider($id),
			'timeVoucherDataProvider' => $this->getTimeVoucherDataProvider($id,$invoiceSearchModel->fromDate,$invoiceSearchModel->toDate,$invoiceSearchModel->summariseReport),
			'unavailability' => $this->getUnavailabilityDataProvider($id)
        ]);
    }

    public function actionCreate()
    {
        $session = Yii::$app->session;
        $model = new UserForm();
        $emailModels = new UserEmail();
        $model->setScenario('create');
        $model->roles = Yii::$app->request->queryParams['role_name'];
        if ($model->roles === User::ROLE_STAFFMEMBER) {
            if (!Yii::$app->user->can('createStaff')) {
                throw new ForbiddenHttpException();
            }
        }
        $request = Yii::$app->request;
        if ($model->load($request->post()) && $model->save() && $emailModels->load($request->post())) {
			if(!empty($emailModels->email))
                        {
                        $userContact = new UserContact();
			$userContact->userId = $model->getModel()->id;
			$userContact->labelId = Label::LABEL_WORK;
			$userContact->isPrimary = true;
			$userContact->save();

			$emailModels->userContactId = $userContact->id;
			$emailModels->save();
                        }
			return $this->redirect(['view', 'UserSearch[role_name]' => $model->roles, 'id' => $model->getModel()->id]);
        }
    }

	public function actionEditProfile($id)
	{
		$request = Yii::$app->request;
		$model = new UserForm();
        $model->setModel($this->findModel($id));	
		$userProfile  = $model->getModel()->userProfile;
		if ($model->load($request->post()) && $userProfile->load($request->post())) {
			if(!empty($model->password)) {
        		$model->getModel()->setPassword($model->password);
			}
			if($model->save()) {
				$userProfile->save();
				return [
				   'status' => true,
				];	
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
        $session = Yii::$app->session;
        $locationId = \Yii::$app->session->get('location_id');
        $roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
        $lastRole = end($roles);
        $adminModel = User::findOne(['id' => $id]);
        $model = User::find()->location($locationId)
                ->where(['user.id' => $id])
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
    
    public function actionEditLesson($lessonId)
    {
        $model = Lesson::findOne(['id' => $lessonId]);
        $teacher = User::findOne($model->teacherId);
        $data  = $this->renderAjax('teacher/_form-lesson', [
            'model' => $model,
            'userModel' => $teacher
        ]); 
        return [
            'status' => true,
            'data' => $data
        ];
    }
	public function deleteContact($id) {
		$model = $this->findModel($id);
		if(!empty($model->emails)) {
			foreach($model->emails as $email) {
				$email->userContact->delete();
				$email->delete();
			}
		}
		if(!empty($model->phoneNumbers)) {
			foreach($model->phoneNumbers as $phone) {
				$phone->userContact->delete();
				$phone->delete();
			}
		}
		if(!empty($model->addresses)) {
			foreach($model->addresses as $address) {
				$address->userContact->delete();
				$address->delete();
			}
		}
		
	}
	public function actionDelete($id)
    {
        $model = new UserForm();
        $model->setModel($this->findModel($id));

        $role = $model->roles;
        if (($role === User::ROLE_TEACHER) && (!Yii::$app->user->can('deleteTeacherProfile'))) {
            throw new ForbiddenHttpException();
        }
        if (($role === User::ROLE_CUSTOMER) && (!Yii::$app->user->can('deleteCustomerProfile'))) {
            throw new ForbiddenHttpException();
        }
        if (($role === User::ROLE_OWNER) && (!Yii::$app->user->can('deleteOwnerProfile'))) {
            throw new ForbiddenHttpException();
        }
        if (($role === User::ROLE_STAFFMEMBER) && (!Yii::$app->user->can('deleteStaffProfile'))) {
            throw new ForbiddenHttpException();
        }
		
		if(in_array($role, [User::ROLE_ADMINISTRATOR, User::ROLE_OWNER, User::ROLE_STAFFMEMBER])) {
			$this->deleteContact($id);
			$model->getModel()->delete();
			$response = [
				'status' => true,
				'url' => Url::to(['index', 'UserSearch[role_name]' => $model->roles]) 
			];	
		}else if($role === User::ROLE_CUSTOMER) {
                    if(empty($model->getModel()->student)) {
				$this->deleteContact($id);
				$model->getModel()->delete();
				$response = [
					'status' => true,
					'url' => Url::to(['index', 'UserSearch[role_name]' => $model->roles]) 
				];
			} else {
				$response = [
					'status' => false,
					'message' => 'Unable to delete. There are student(s) associated with this ' . $role
				];
			}
		}else if($role === User::ROLE_TEACHER) {
                   if(empty($model->getModel()->qualifications) && empty($model->getModel()->courses)) {
				$this->deleteContact($id);
				$model->getModel()->delete();
				$response = [
					'status' => true,
					'url' => Url::to(['index', 'UserSearch[role_name]' => $model->roles]) 
				];
			} else {
				$response = [
					'status' => false,
					'message' => 'Unable to delete. There are qualification/course(s) associated with this ' . $role
				];
			}
		}
		return $response;
    }
}
