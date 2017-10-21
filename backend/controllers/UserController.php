<?php

namespace backend\controllers;

use Yii;
use common\models\User;
use common\models\CompanyAccount;
use common\models\Address;
use common\models\PhoneNumber;
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
use common\models\LocationAvailability;
use common\models\InvoiceLineItem;
use common\models\UserEmail;
use common\models\Label;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;
use common\models\Payment;
use common\models\UserAddress;
/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
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
                'only' => ['edit-profile', 'edit-phone', 'edit-address', 'edit-email', 'edit-lesson'],
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
                ->where(['lesson.status' => [Lesson::STATUS_SCHEDULED, Lesson::STATUS_COMPLETED, Lesson::STATUS_MISSED]])
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
			->andWhere(['status' => [Lesson::STATUS_COMPLETED, Lesson::STATUS_MISSED, Lesson::STATUS_SCHEDULED]])
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
        $locationId = $session->get('location_id');
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

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
	public function saveAddressAndPhone($model, $emailModels, $addressModels, $phoneNumberModels, $qualificationModels)
	{
        $transaction = \Yii::$app->db->beginTransaction();
		if ($flag = $model->save(false)) {
			foreach ($addressModels as $addressModel) {
				if (!($flag = $addressModel->save(false))) {
					$transaction->rollBack();
					break;
				}
				$model->getModel()->link('addresses', $addressModel);
			}
                        foreach ($emailModels as $emailModel) {
                                if (!is_numeric($emailModel->labelId)) {
                                    $label = new Label();
                                    $label->name = $emailModel->labelId;
                                    $label->userAdded = $model->getModel()->id;
                                    if (!($flag = $label->save(false))) {
					$transaction->rollBack();
					break;
                                    }
                                    $emailModel->labelId = $label->id;
                                }
                                $emailModel->userId = $model->getModel()->id;
				if (!($flag = $emailModel->save(false))) {
					$transaction->rollBack();
					break;
				}
			}
			foreach ($phoneNumberModels as $phoneNumberModel) {
                                if (!is_numeric($phoneNumberModel->label_id)) {
                                    $label = new Label();
                                    $label->name = $phoneNumberModel->label_id;
                                    $label->userAdded = $model->getModel()->id;
                                    if (!($flag = $label->save(false))) {
					$transaction->rollBack();
					break;
                                    }
                                    $phoneNumberModel->label_id = $label->id;
                                }
				$phoneNumberModel->user_id = $model->getModel()->id;
				if (!($flag = $phoneNumberModel->save(false))) {
					$transaction->rollBack();
					break;
				}
			}
			foreach ($qualificationModels as $qualificationModel) {
				$qualification = new Qualification();
				$qualification->program_id = $qualificationModel['program_id'];
				$qualification->rate = $qualificationModel['rate'];
				$qualification->teacher_id = $model->getModel()->id;
				$qualification->isDeleted = false;
				$qualification->type = Qualification::TYPE_HOURLY;	
				if($qualification->program->isGroup()) {
					$qualification->type = Qualification::TYPE_FIXED;	
				}
				$qualification->save();
			}
		}
        $transaction->commit();
		return $flag;
	}
    public function actionCreate()
    {
        $session = Yii::$app->session;
        $locationId = $session->get('location_id');
        $model = new UserForm();
        $addressModels = new Address();
        $userAddress = new UserAddress();
        $phoneNumberModels = new PhoneNumber();
        $emailModels = new UserEmail();
        $model->setScenario('create');
        $model->roles = Yii::$app->request->queryParams['role_name'];
        if ($model->roles === User::ROLE_STAFFMEMBER) {
            if (!Yii::$app->user->can('createStaff')) {
                throw new ForbiddenHttpException();
            }
        }
        $request = Yii::$app->request;
        if ($model->load($request->post()) && $model->save()) {
            if ($addressModels->load($request->post()) && $phoneNumberModels->load($request->post()) && $emailModels->load($request->post())) {
                
                $addressModels->is_primary = true;
                $addressModels->save();
                $userAddress->address_id = $addressModels->id;
                $userAddress->user_id = $model->getModel()->id;
                $userAddress->save();
                $phoneNumberModels->user_id = $model->getModel()->id;
                $phoneNumberModels->is_primary = true;
                $phoneNumberModels->save();
                $emailModels->userId = $model->getModel()->id;
                $emailModels->isPrimary = true;
                $emailModels->save();
                return $this->redirect(['view', 'UserSearch[role_name]' => $model->roles, 'id' => $model->getModel()->id]);
            }
        }
    }

	public function actionEditProfile($id)
	{
		$request = Yii::$app->request;
		$model = new UserForm();
        $model->setModel($this->findModel($id));	
		if ($model->load($request->post())) {
			if($model->save()) {
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
        
    public function actionEditEmail($id)
    {
        $request = Yii::$app->request;
        $model = new UserForm();
        $model->setModel($this->findModel($id));
        $emailModels = $model->emails;
        $data = $this->renderAjax('update/_email', [
            'model' => $model,
            'emailModels' => $emailModels,
        ]);

        if ($request->isPost) {
            $oldEmailIDs = ArrayHelper::map($emailModels, 'id', 'id');
            $emailModels = UserForm::createMultiple(UserEmail::classname(), $emailModels);
            Model::loadMultiple($emailModels, $request->post());
            $deletedEmailIDs = array_diff($oldEmailIDs, array_filter(ArrayHelper::map($emailModels, 'id', 'id')));

            $valid = Model::validateMultiple($emailModels);
            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if (!empty($deletedEmailIDs)) {
                        UserEmail::deleteAll(['id' => $deletedEmailIDs]);
                    }
                    foreach ($emailModels as $emailModel) {
                        if (!is_numeric($emailModel->labelId)) {
                            $label = new Label();
                            $label->name = $emailModel->labelId;
                            $label->userAdded = $model->getModel()->id;
                            if (!($flag = $label->save(false))) {
                                
                                $transaction->rollBack();
                                break;
                            }
                            $emailModel->labelId = $label->id;
                        }
                        $emailModel->userId = $id;
                        if (!$emailModel->save(false)) {
                            $transaction->rollBack();
                            break;
                        }
                    }

                    $transaction->commit();
                    return [
                        'status' => true,
                    ]; 
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            } 
        } else {
            return [
                'status' => true,
                'data' => $data,
            ];
        }
    }
        
	public function actionEditPhone($id)
	{
		$request = Yii::$app->request;
		$model = new UserForm();
        $model->setModel($this->findModel($id));	
        $phoneNumberModels = $model->phoneNumbers;
		$data = $this->renderAjax('update/_phone', [
			'model' => $model,
			'phoneNumberModels' => $phoneNumberModels,
		]);
		
        $response = Yii::$app->response;
        if ($request->isPost) {
            $oldPhoneIDs = ArrayHelper::map($phoneNumberModels, 'id', 'id');
            $phoneNumberModels = UserForm::createMultiple(PhoneNumber::classname(), $phoneNumberModels);
            Model::loadMultiple($phoneNumberModels, $request->post());
            $deletedPhoneIDs = array_diff($oldPhoneIDs, array_filter(ArrayHelper::map($phoneNumberModels, 'id', 'id')));

            $valid = Model::validateMultiple($phoneNumberModels);
            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
					if (!empty($deletedPhoneIDs)) {
						PhoneNumber::deleteAll(['id' => $deletedPhoneIDs]);
					}
					foreach ($phoneNumberModels as $phoneNumberModel) {
                                            if (!is_numeric($phoneNumberModel->label_id)) {
                                                $label = new Label();
                                                $label->name = $phoneNumberModel->label_id;
                                                $label->userAdded = $model->getModel()->id;
                                                if (!($flag = $label->save(false))) {

                                                    $transaction->rollBack();
                                                    break;
                                                }
                                                $phoneNumberModel->label_id = $label->id;
                                            }
						$phoneNumberModel->user_id = $id;
						if (!$phoneNumberModel->save(false)) {
							$transaction->rollBack();
							break;
						}
					}
                    
					$transaction->commit();
					return [
						'status' => true,
					]; 
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            } 
        } else {
			return [
				'status' => true,
				'data' => $data,
			];
		}
	}
	public function actionEditAddress($id)
	{
		$request = Yii::$app->request;
		$model = new UserForm();
        $model->setModel($this->findModel($id));	
        $addressModels = $model->addresses;
		$data = $this->renderAjax('update/_address', [
			'model' => $model,
			'addressModels' => $addressModels,
		]);
		
        if ($request->isPost) {
            $oldAddressIDs = ArrayHelper::map($addressModels, 'id', 'id');
            $addressModels = UserForm::createMultiple(Address::classname(), $addressModels);
            Model::loadMultiple($addressModels, $request->post());
            $deletedAddressIDs = array_diff($oldAddressIDs, array_filter(ArrayHelper::map($addressModels, 'id', 'id')));

            $valid = Model::validateMultiple($addressModels);
            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
					if (!empty($deletedAddressIDs)) {
						Address::deleteAll(['id' => $deletedAddressIDs]);
					}
					foreach ($addressModels as $addressModel) {
						if (!$addressModel->save(false)) {
							$transaction->rollBack();
							break;
						}
						$model->getModel()->link('addresses', $addressModel);
					}
                    
					$transaction->commit();
					return [
						'status' => true,
					]; 
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            } 
        } else {
			return [
				'status' => true,
				'data' => $data,
			];
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
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param int $id
     *
     * @return mixed
     */
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

        $db = Yii::$app->db;
        $command = $db->createCommand('DELETE u, up, pn, ua, a,ul,raa  FROM `user` u
			LEFT JOIN `user_profile` up ON u.`id` = up.`user_id`
			LEFT JOIN `phone_number` pn ON u.`id` = pn.`user_id`
			LEFT JOIN `user_address` ua ON u.`id` = ua.`user_id` 
			LEFT JOIN `user_location` ul ON ul.`user_id` = u.`id`           
			LEFT JOIN `address` a ON a.`id` = ua.`address_id` 
			LEFT JOIN `rbac_auth_assignment` raa ON raa.`user_id` = u.`id`  
			WHERE u.`id` = :id', [':id' => $id]);
        $command->execute();

        Yii::$app->session->setFlash('alert', [
            'options' => ['class' => 'alert-success'],
            'body' => ucwords($model->roles).' profile has been deleted successfully',
        ]);

        return $this->redirect(['index', 'UserSearch[role_name]' => $model->roles]);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionDeleteAllStaffMembers()
    {
        $db = Yii::$app->db;
        $command = $db->createCommand("DELETE u, up, pn, ua, a,raa  FROM `user` u
			LEFT JOIN `user_profile` up ON u.`id` = up.`user_id`
			LEFT JOIN `phone_number` pn ON u.`id` = pn.`user_id`
			LEFT JOIN `user_address` ua ON u.`id` = ua.`user_id`
			LEFT JOIN `address` a ON a.`id` = ua.`address_id` 
			LEFT JOIN `rbac_auth_assignment` raa ON raa.`user_id` = u.`id`  
			WHERE raa.`item_name` = 'staffmember'");
        $command->execute();

        Yii::$app->session->setFlash('alert', [
            'options' => ['class' => 'alert-success'],
            'body' => Yii::t('backend', 'All staffmembers records have been deleted successfully ', []),
        ]);

        return $this->redirect(['index', 'UserSearch[role_name]' => User::ROLE_STAFFMEMBER]);
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
        $locationId = $session->get('location_id');
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
}
