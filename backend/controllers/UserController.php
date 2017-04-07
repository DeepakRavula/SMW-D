<?php

namespace backend\controllers;

use yii\filters\ContentNegotiator;
use common\models\Payment;
use Yii;
use common\models\User;
use common\models\TeacherRoom;
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
use common\models\ItemType;
use common\models\TaxStatus;
use common\models\PaymentMethod;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;
use common\models\TeacherRate;

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
            'contentNegotiator' => [
               'class' => ContentNegotiator::className(),
               'only' => ['edit-teacher-availability', 'add-teacher-availability', 'teacher-availability-events',
                   'delete-teacher-availability', 'modify-teacher-availability'],
               'formatParam' => '_format',
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
        $db = $searchModel->search(Yii::$app->request->queryParams);

        $query = Student::find()
            ->andWhere(['customer_id' => $id])
			->active();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query = Student::find()
                ->teacherStudents($locationId, $model->id)
				->active();

        $studentDataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query = Location::find()
                ->joinWith('userLocations')
                ->where(['user_id' => $id]);
        $locationDataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query = TeacherAvailability::find()
                ->joinWith('userLocation')
                ->where(['user_id' => $id]);
        $teacherDataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $program = null;
        $qualifications = Qualification::find()
                ->joinWith('program')
                ->where(['teacher_id' => $id])
                ->all();
        foreach ($qualifications as $qualification) {
            $program .= "{$qualification->program->name}, ";
        }
        $program = substr($program, 0, -2);

        $addressDataProvider = new ActiveDataProvider([
            'query' => $model->getAddresses(),
            'sort' => [
            'defaultOrder' => [
            'is_primary' => SORT_DESC,
        ],
    ],
            ]);
        $phoneDataProvider = new ActiveDataProvider([
            'query' => $model->getPhoneNumbers(),
            'sort' => [
            'defaultOrder' => [
            'is_primary' => SORT_DESC,
             ],
                ],
            ]);

        $currentDate = new \DateTime();
        $lessonQuery = Lesson::find()
                ->location($locationId)
                ->student($id)
                ->where(['lesson.status' => [Lesson::STATUS_SCHEDULED, Lesson::STATUS_COMPLETED]])
                ->notDeleted();

        $lessonDataProvider = new ActiveDataProvider([
            'query' => $lessonQuery,
        ]);

        $enrolmentQuery = Enrolment::find()
            ->location($locationId)
            ->joinWith(['student' => function ($query) use ($model) {
                $query->where(['customer_id' => $model->id])
                ->active();
            }])
            ->notDeleted();

        $enrolmentDataProvider = new ActiveDataProvider([
            'query' => $enrolmentQuery,
        ]);

		$request = Yii::$app->request;
        $currentDate = new \DateTime();
        $model->fromDate = $currentDate->format('1-m-Y');
        $model->toDate = $currentDate->format('t-m-Y');
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

        $invoiceDataProvider = new ActiveDataProvider([
            'query' => $invoiceQuery,
        ]);

        $proFormaInvoiceQuery = Invoice::find()
                ->where([
					'invoice.user_id' => $model->id,
                    'invoice.type' => Invoice::TYPE_PRO_FORMA_INVOICE,
                    'invoice.location_id' => $locationId,
                ])
				->notDeleted();

        $unscheduledLessons = Lesson::find()
			->enrolled()
            ->joinWith(['privateLesson'])
            ->orderBy(['private_lesson.expiryDate' => SORT_DESC])
			->andWhere(['lesson.teacherId' => $id])
			->unscheduled()
			->notDeleted();

        $unscheduledLessonDataProvider = new ActiveDataProvider([
            'query' => $unscheduledLessons,
        ]);

        $proFormaInvoiceDataProvider = new ActiveDataProvider([
            'query' => $proFormaInvoiceQuery,
        ]);

        $paymentDataProvider = new ActiveDataProvider([
            'query' => payment::find()
                ->where(['user_id' => $model->id]),
        ]);

        $openingBalanceCredit = Invoice::find()
                ->joinWith(['lineItems' => function ($query) {
                    $query->where(['item_type_id' => ItemType::TYPE_OPENING_BALANCE]);
                }])
                ->where(['invoice.user_id' => $model->id])
                ->andWhere(['<', 'invoice.balance', 0])
				->notDeleted()
                ->one();
        $positiveOpeningBalanceModel = Invoice::find()
                ->joinWith(['lineItems' => function ($query) {
                    $query->where(['item_type_id' => ItemType::TYPE_OPENING_BALANCE]);
                }])
                ->joinWith('payments')
                ->where(['invoice.user_id' => $model->id, 'payment.id' => null])
				->notDeleted()
                ->one();

        $openingBalanceQuery = Payment::find()
                ->joinWith(['invoicePayment ip' => function ($query) {
                    $query->joinWith(['invoice' => function ($query) {
                        $query->joinWith(['lineItems' => function ($query) {
                            $query->where(['item_type_id' => ItemType::TYPE_OPENING_BALANCE]);
                        }]);
                    }]);
                }])
                ->where(['payment.user_id' => $model->id, 'payment_method_id' => PaymentMethod::TYPE_CREDIT_USED]);

        $openingBalanceDataProvider = new ActiveDataProvider([
            'query' => $openingBalanceQuery,
        ]);
		$request = Yii::$app->request;
		$lessonSearch = new LessonSearch();
		$lessonSearch->fromDate = new \DateTime();
		$lessonSearch->toDate = new \DateTime();
		$lessonSearchModel = $request->get('LessonSearch');
		
		if(!empty($lessonSearchModel)) {
			$lessonSearch->fromDate = new \DateTime($lessonSearchModel['fromDate']);
			$lessonSearch->toDate = new \DateTime($lessonSearchModel['toDate']);
			$lessonSearch->summariseReport = $lessonSearchModel['summariseReport']; 
		}
		$teacherLessons = Lesson::find()
			->innerJoinWith('enrolment')
			->location($locationId)
			->where(['lesson.teacherId' => $model->id])
			->notDraft()
			->notDeleted()
			->andWhere(['status' => [Lesson::STATUS_COMPLETED, Lesson::STATUS_MISSED, Lesson::STATUS_SCHEDULED]])
			->between($lessonSearch->fromDate, $lessonSearch->toDate);
			if($lessonSearch->summariseReport) {
				$teacherLessons->groupBy('DATE(date)');	
			} else {
				$teacherLessons->orderBy(['date' => SORT_ASC]);
			}
			
		$teacherLessonDataProvider = new ActiveDataProvider([
			'query' => $teacherLessons,
			'pagination' => false,
		]);

		$notes = Note::find()
			->where(['instanceId' => $model->id, 'instanceType' => Note::INSTANCE_TYPE_USER])
			->orderBy(['createdOn' => SORT_DESC]);

        $noteDataProvider = new ActiveDataProvider([
            'query' => $notes,
        ]);
		$teachersAvailabilities = TeacherAvailability::find()
            ->joinWith(['userLocation' => function ($query) use ($locationId, $model) {
                $query->andWhere(['user_location.location_id' => $locationId, 'user_id' => $model->id]);
            }])
            ->groupBy('day')
            ->all();

        $account = CustomerAccount::find()
            ->where(['userId' => $id])
            ->orderBy(['id' => SORT_ASC]);

        $accountDataProvider = new ActiveDataProvider([
            'query' => $account,
        ]);

        return $this->render('view', [
            'minTime' => $minTime,
            'maxTime' => $maxTime,
            'student' => new Student(),
            'dataProvider' => $dataProvider,
            'teacherDataProvider' => $teacherDataProvider,
            'model' => $model,
            'searchModel' => $searchModel,
            'lessonSearchModel' => $lessonSearch,
            'program' => $program,
            'addressDataProvider' => $addressDataProvider,
            'phoneDataProvider' => $phoneDataProvider,
            'lessonDataProvider' => $lessonDataProvider,
            'locationDataProvider' => $locationDataProvider,
            'enrolmentDataProvider' => $enrolmentDataProvider,
            'invoiceDataProvider' => $invoiceDataProvider,
            'studentDataProvider' => $studentDataProvider,
            'paymentDataProvider' => $paymentDataProvider,
            'openingBalanceDataProvider' => $openingBalanceDataProvider,
            'openingBalanceCredit' => $openingBalanceCredit,
            'proFormaInvoiceDataProvider' => $proFormaInvoiceDataProvider,
            'unscheduledLessonDataProvider' => $unscheduledLessonDataProvider,
            'positiveOpeningBalanceModel' => $positiveOpeningBalanceModel,
            'teacherLessonDataProvider' => $teacherLessonDataProvider,
            'noteDataProvider' => $noteDataProvider,
            'accountDataProvider' => $accountDataProvider,
            'teachersAvailabilities' => $teachersAvailabilities
        ]);
    }

    public function actionAddOpeningBalance($id)
    {
        $model = $this->findModel($id);
        $locationId = Yii::$app->session->get('location_id');
        $paymentModel = new Payment(['scenario' => Payment::SCENARIO_OPENING_BALANCE]);
        if ($paymentModel->load(Yii::$app->request->post())) {
            $invoice = new Invoice();
            $invoice->user_id = $model->id;
            $invoice->location_id = $locationId;
            $invoice->type = Invoice::TYPE_INVOICE;
            $invoice->save();

            $invoiceLineItem = new InvoiceLineItem(['scenario' => InvoiceLineItem::SCENARIO_OPENING_BALANCE]);
            $invoiceLineItem->invoice_id = $invoice->id;
            $invoiceLineItem->item_id = Invoice::ITEM_TYPE_OPENING_BALANCE;
            $invoiceLineItem->item_type_id = ItemType::TYPE_OPENING_BALANCE;
            $invoiceLineItem->description = 'Opening Balance';
            $invoiceLineItem->unit = 1;
            $invoiceLineItem->amount = $paymentModel->amount;
            $invoiceLineItem->save();

            if ($paymentModel->amount > 0) {
                $invoice->subTotal = $invoiceLineItem->amount;
            } else {
                $invoice->subTotal = 0.00;
            }
            $invoice->tax = $invoiceLineItem->tax_rate;
            $invoice->total = $invoice->subTotal + $invoice->tax;
            $invoice->save();

            if ($paymentModel->amount < 0) {
                $paymentModel->invoiceId = $invoice->id;
                $paymentModel->payment_method_id = PaymentMethod::TYPE_ACCOUNT_ENTRY;
                $paymentModel->amount = abs($paymentModel->amount);
                $paymentModel->save();
            }
            Yii::$app->session->setFlash('alert', [
                'options' => ['class' => 'alert-success'],
                'body' => 'Invoice has been created successfully',
            ]);

            return $this->redirect(['invoice/view', 'id' => $invoice->id]);
        }
    }
    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
	public function saveAddressAndPhone($model, $addressModels, $phoneNumberModels)
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

			foreach ($phoneNumberModels as $phoneNumberModel) {
				$phoneNumberModel->user_id = $model->getModel()->id;
				if (!($flag = $phoneNumberModel->save(false))) {
					$transaction->rollBack();
					break;
				}
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
        $addressModels = [new Address()];
        $phoneNumberModels = [new PhoneNumber()];

        $model->setScenario('create');
        $model->roles = Yii::$app->request->queryParams['User']['role_name'];
        if ($model->roles === User::ROLE_STAFFMEMBER) {
            if (!Yii::$app->user->can('createStaff')) {
                throw new ForbiddenHttpException();
            }
        }

        $request = Yii::$app->request;
        $response = Yii::$app->response;
        if ($model->load($request->post())) {
			$addressModels = UserForm::createMultiple(Address::classname());
	        Model::loadMultiple($addressModels, $request->post());	
            $phoneNumberModels = UserForm::createMultiple(PhoneNumber::classname());
            Model::loadMultiple($phoneNumberModels, $request->post());

            if ($request->isAjax) {
                $response->format = Response::FORMAT_JSON;

                return ArrayHelper::merge(
                        ActiveForm::validate($model), ActiveForm::validateMultiple($addressModels), ActiveForm::validateMultiple($phoneNumberModels)
                );
            }
            $valid = $model->validate();
            $valid = (Model::validateMultiple($addressModels) || Model::validateMultiple($phoneNumberModels)) && $valid;

            if ($valid) {
                try {
					$success = $this->saveAddressAndPhone($model, $addressModels, $phoneNumberModels);
                    if ($success) {
                        Yii::$app->session->setFlash('alert', [
                                'options' => ['class' => 'alert-success'],
                                'body' => ucwords($model->roles).' profile has been created successfully',
                        ]);

                        return $this->redirect(['view', 'UserSearch[role_name]' => $model->roles, 'id' => $model->getModel()->id]);
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }
        }

        return $this->render('create', [
			'model' => $model,
			'roles' => ArrayHelper::map(Yii::$app->authManager->getRoles(), 'name', 'name'),
			'programs' => ArrayHelper::map(Program::find()->privateProgram()->active()->all(), 'id', 'name'),
			'addressModels' => (empty($addressModels)) ? [new Address()] : $addressModels,
			'phoneNumberModels' => (empty($phoneNumberModels)) ? [new PhoneNumber()] : $phoneNumberModels,
			'locations' => ArrayHelper::map(Location::find()->all(), 'id', 'name'),
        ]);
    }

    /**
     * Updates an existing User model.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $session = Yii::$app->session;
        $locationId = $session->get('location_id');

        $model = new UserForm();
        $model->setModel($this->findModel($id));
        $user = $this->findModel($id);
        $ownProfile = true;
        if (!Yii::$app->user->can('updateOwnProfile', ['model' => $user])) {
            $ownProfile = false;
        }
        if ((!$ownProfile)) {
            $role = $model->roles;
            if (($role === User::ROLE_TEACHER) && (!Yii::$app->user->can('updateTeacherProfile'))) {
                throw new ForbiddenHttpException();
            }
            if (($role === User::ROLE_CUSTOMER) && (!Yii::$app->user->can('updateCustomerProfile'))) {
                throw new ForbiddenHttpException();
            }
            if (($role === User::ROLE_OWNER) && (!Yii::$app->user->can('updateOwnerProfile'))) {
                throw new ForbiddenHttpException();
            }
            if (($role === User::ROLE_STAFFMEMBER) && (!Yii::$app->user->can('updateStaffProfile'))) {
                throw new ForbiddenHttpException();
            }
        }

        $addressModels = $model->addresses;
        $phoneNumberModels = $model->phoneNumbers;

        $request = Yii::$app->request;
        $response = Yii::$app->response;
        if ($model->load($request->post())) {
            $oldAddressIDs = ArrayHelper::map($addressModels, 'id', 'id');
            $addressModels = UserForm::createMultiple(Address::classname(), $addressModels);
            Model::loadMultiple($addressModels, $request->post());
            $deletedAddressIDs = array_diff($oldAddressIDs, array_filter(ArrayHelper::map($addressModels, 'id', 'id')));

            $oldPhoneIDs = ArrayHelper::map($phoneNumberModels, 'id', 'id');
            $phoneNumberModels = UserForm::createMultiple(PhoneNumber::classname(), $phoneNumberModels);
            Model::loadMultiple($phoneNumberModels, $request->post());
            $deletedPhoneIDs = array_diff($oldPhoneIDs, array_filter(ArrayHelper::map($phoneNumberModels, 'id', 'id')));


            if ($request->isAjax) {
                $response->format = Response::FORMAT_JSON;

                return ArrayHelper::merge(
                        ActiveForm::validate($model), ActiveForm::validateMultiple($addressModels), ActiveForm::validateMultiple($phoneNumberModels)
                );
            }
            $valid = $model->validate();
            $valid = (Model::validateMultiple($addressModels) && Model::validateMultiple($phoneNumberModels)) && $valid;

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save(false)) {
                        if (!empty($deletedAddressIDs)) {
                            Address::deleteAll(['id' => $deletedAddressIDs]);
                        }
                        foreach ($addressModels as $addressModel) {
                            if (!($flag = $addressModel->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                            $model->getModel()->link('addresses', $addressModel);
                        }
                        if (!empty($deletedPhoneIDs)) {
                            PhoneNumber::deleteAll(['id' => $deletedPhoneIDs]);
                        }
                        foreach ($phoneNumberModels as $phoneNumberModel) {
                            $phoneNumberModel->user_id = $id;
                            if (!($flag = $phoneNumberModel->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                    }
                    if ($flag) {
                        $transaction->commit();
                        Yii::$app->session->setFlash('alert', [
                                'options' => ['class' => 'alert-success'],
                                'body' => ucwords($model->roles).' profile has been updated successfully',
                        ]);
                        $section = ltrim($model->section, '#');

                        return $this->redirect(['view', 'UserSearch[role_name]' => $model->roles, 'id' => $model->getModel()->id, '#' => $section]);
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }
        }

        return $this->render('update', [
                    'model' => $model,
                    'roles' => ArrayHelper::map(Yii::$app->authManager->getRoles(), 'name', 'name'),
                    'programs' => ArrayHelper::map(Program::find()->privateProgram()->active()->all(), 'id', 'name'),
                    'locations' => ArrayHelper::map(Location::find()->all(), 'id', 'name'),
                    'addressModels' => (empty($addressModels)) ? [new Address()] : $addressModels,
                    'phoneNumberModels' => (empty($phoneNumberModels)) ? [new PhoneNumber()] : $phoneNumberModels,
        ]);
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
    public function actionDeleteAllCustomer()
    {
        $db = Yii::$app->db;
        $command = $db->createCommand("DELETE u, up, pn, ua, a,s,raa  FROM `user` u
			LEFT JOIN `user_profile` up ON u.`id` = up.`user_id`
			LEFT JOIN `phone_number` pn ON u.`id` = pn.`user_id`
			LEFT JOIN `user_address` ua ON u.`id` = ua.`user_id` 
			LEFT JOIN `student` s ON s.`customer_id` = u.`id`           
			LEFT JOIN `address` a ON a.`id` = ua.`address_id` 
			LEFT JOIN `rbac_auth_assignment` raa ON raa.`user_id` = u.`id`  
			WHERE raa.`item_name` = 'customer'");
        $command->execute();

        Yii::$app->session->setFlash('alert', [
            'options' => ['class' => 'alert-success'],
            'body' => Yii::t('backend', 'All customer and student records have been deleted successfully ', []),
        ]);

        return $this->redirect(['index', 'UserSearch[role_name]' => User::ROLE_CUSTOMER]);
    }

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
                ->one();
        if ($model !== null) {
            return $model;
        } elseif ($lastRole->name === User::ROLE_ADMINISTRATOR && $adminModel != null) {
            return $adminModel;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

	public function actionPrint($id)
    {
        $model = $this->findModel($id);
        $session = Yii::$app->session;
        $locationId = $session->get('location_id');
		$request = Yii::$app->request;
		$lessonSearch = new LessonSearch();
		$lessonSearch->fromDate = new \DateTime();
		$lessonSearch->toDate = new \DateTime();
		$lessonSearchModel = $request->get('LessonSearch');
		
		if(!empty($lessonSearchModel)) {
			$lessonSearch->fromDate = new \DateTime($lessonSearchModel['fromDate']);
			$lessonSearch->toDate = new \DateTime($lessonSearchModel['toDate']);
			$lessonSearch->summariseReport = $lessonSearchModel['summariseReport']; 
		}
		$teacherLessons = Lesson::find()
			->innerJoinWith('enrolment')
			->location($locationId)
			->where(['lesson.teacherId' => $model->id])
			->notDraft()
			->notDeleted()
			->andWhere(['status' => [Lesson::STATUS_COMPLETED, Lesson::STATUS_MISSED, Lesson::STATUS_SCHEDULED]])
			->between($lessonSearch->fromDate, $lessonSearch->toDate);
			if($lessonSearch->summariseReport) {
				$teacherLessons->groupBy('DATE(date)');	
			} else {
				$teacherLessons->orderBy(['date' => SORT_ASC]);
			}
			
		$teacherLessonDataProvider = new ActiveDataProvider([
			'query' => $teacherLessons,
			'pagination' => false,
		]);
		
        $this->layout = '/print';

        return $this->render('teacher/_print', [
			'model' => $model,
			'teacherLessonDataProvider' => $teacherLessonDataProvider,
			'fromDate' => $lessonSearch->fromDate,
			'toDate' => $lessonSearch->toDate,
			'searchModel' => $lessonSearch
        ]);
    }

	public function actionInvoicePrint($id)
    {
        $model = $this->findModel($id);
        $session = Yii::$app->session;
        $locationId = $session->get('location_id');
		$request = Yii::$app->request;
        $currentDate = new \DateTime();
        $model->fromDate = $currentDate->format('1-m-Y');
        $model->toDate = $currentDate->format('t-m-Y');
        $model->dateRange = $model->fromDate . ' - ' . $model->toDate;
        $userRequest = $request->get('User');
		if(!empty($userRequest)) {
			$model->dateRange = $userRequest['dateRange']; 
			list($model->fromDate, $model->toDate) = explode(' - ', $userRequest['dateRange']);
			$invoiceStatus = $userRequest['invoiceStatus'];
			$studentId = $userRequest['studentId'];
		} 
		$fromDate =  (new \DateTime($model->fromDate))->format('Y-m-d');
        $toDate =(new \DateTime($model->toDate))->format('Y-m-d');
        $invoiceQuery = Invoice::find()
                ->where([
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
        $invoiceDataProvider = new ActiveDataProvider([
            'query' => $invoiceQuery,
			'pagination' => false,
        ]);
        $this->layout = '/print';

        return $this->render('customer/_print', [
			'model' => $model,
			'invoiceDataProvider' => $invoiceDataProvider,
			'dateRange' => $model->dateRange,
        ]);
    }

    public function actionTeacherAvailabilityEvents($id)
    {
        $session    = Yii::$app->session;
        $locationId = $session->get('location_id');
        $location   = Location::findOne($locationId);
        $events     = [];
        foreach ($location->locationAvailabilities as $availability) {
            $startTime = new \DateTime($availability->fromTime);
            $endTime   = new \DateTime($availability->toTime);
            $events[]  = [
                'resourceId' => $availability->day,
                'start'      => $startTime->format('Y-m-d H:i:s'),
                'end'        => $endTime->format('Y-m-d H:i:s'),
                'rendering'  => 'background',
                'backgroundColor' => '#ffffff',
            ];
        }
        $teacherAvailabilities = TeacherAvailability::find()
                ->joinWith('userLocation')
                ->where(['user_id' => $id])
                ->all();
        foreach ($teacherAvailabilities as $teacherAvailability) {
            $title = null;
            if (!empty($teacherAvailability->teacherRoom->classroom->name)) {
                $title = $teacherAvailability->teacherRoom->classroom->name;
            }
            $startTime = new \DateTime($teacherAvailability->from_time);
            $endTime   = new \DateTime($teacherAvailability->to_time);
            $events[]  = [
                'title'      => $title,
                'id'         => $teacherAvailability->id,
                'resourceId' => $teacherAvailability->day,
                'start'      => $startTime->format('Y-m-d H:i:s'),
                'end'        => $endTime->format('Y-m-d H:i:s'),
                'backgroundColor' => '#97ef83',
            ];
        }
        return $events;
    }

    public function actionEditTeacherAvailability($id, $resourceId, $startTime, $endTime)
    {
        $availabilityModel            = TeacherAvailability::findOne($id);
        $availabilityModel->day       = $resourceId;
        $availabilityModel->from_time = $startTime;
        $availabilityModel->to_time   = $endTime;
        if (!empty($availabilityModel->teacherRoom)) {
            $roomModel = $availabilityModel->teacherRoom;
        } else {
            $roomModel = new TeacherRoom();
            $roomModel->teacherAvailabilityId = $id;
        }
        $roomModel->setScenario(TeacherRoom::SCENARIO_AVAILABIITY_EDIT);
        $roomModel->availabilityId = $id;
        $roomModel->from_time = $startTime;
        $roomModel->to_time = $endTime;
        $roomModel->day = $resourceId;
        $roomModel->teacher_location_id = $availabilityModel->teacher_location_id;
        if ($roomModel->validate()) {
            $availabilityModel->save();
            return  [
                'status' => true,
            ];
        } else {
            $errors = ActiveForm::validate($roomModel);
            return [
                'status' => false,
                'errors' => $errors
            ];
        }
    }

    public function actionDeleteTeacherAvailability($id)
    {
        $availabilityModel = TeacherAvailability::findOne($id);
        return [
            'status' => $availabilityModel->delete()
        ];
    }

    public function actionModifyTeacherAvailability($id, $teacherId)
    {
        $teacherModel = User::findOne($teacherId);
        $teacherAvailabilityModel = TeacherAvailability::findOne($id);
        if (empty ($teacherAvailabilityModel)) {
            $teacherAvailabilityModel = new TeacherAvailability();
            $teacherAvailabilityModel->teacher_location_id = $teacherModel->userLocation->id;
            $roomModel = new TeacherRoom();
        } else if (empty ($teacherAvailabilityModel->teacherRoom)) {
            $roomModel = new TeacherRoom();
        } else {
            $roomModel = $teacherAvailabilityModel->teacherRoom;
        }
        if (!empty($teacherAvailabilityModel)) {
            $roomModel->availabilityId = $teacherAvailabilityModel->id;
        }
        $roomModel->teacher_location_id = $teacherModel->userLocation->id;
        $fromTime         = new \DateTime($teacherAvailabilityModel->from_time);
        $toTime           = new \DateTime($teacherAvailabilityModel->to_time);
        $roomModel->from_time = $fromTime->format('g:i A');
        $roomModel->to_time   = $toTime->format('g:i A');
        $post             = Yii::$app->request->post();
        $roomModel->setScenario(TeacherRoom::SCENARIO_AVAILABIITY_EDIT);
        $roomModel->day = $teacherAvailabilityModel->day;
        $data =  $this->renderAjax('teacher/_form-teacher-availability', [
            'model' => $teacherModel,
            'roomModel' => $roomModel,
            'teacherAvailabilityModel' => $teacherAvailabilityModel,
        ]);
        if ($roomModel->load($post)) {
            $fromTime         = new \DateTime($roomModel->from_time);
            $toTime           = new \DateTime($roomModel->to_time);
            $teacherAvailabilityModel->from_time = $fromTime->format('H:i:s');
            $teacherAvailabilityModel->to_time   = $toTime->format('H:i:s');
            $teacherAvailabilityModel->day = $roomModel->day;
            if ($roomModel->validate()) {
                $teacherAvailabilityModel->save();
                if (!empty($roomModel->classroomId)) {
                    $roomModel->availabilityId = $teacherAvailabilityModel->id;
                    $roomModel->teacherAvailabilityId = $teacherAvailabilityModel->id;
                    $roomModel->save();
                } else {
                    TeacherRoom::deleteAll(['teacherAvailabilityId' => $teacherAvailabilityModel->id]);
                }

                return  [
                    'status' => true,
                ];
            } else {
                $errors = ActiveForm::validate($roomModel);
                return [
                    'status' => false,
                    'errors' => $errors,
                ];
            }
        } else {
            return [
                'status' => true,
                'data' => $data
            ];
        }
    }
}
