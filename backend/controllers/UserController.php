<?php

namespace backend\controllers;
use common\models\Payment;
use Yii;
use common\models\User;
use common\models\UserLocation;
use common\models\Address;
use common\models\PhoneNumber;
use common\models\TeacherAvailability;
use common\models\Qualification;
use common\models\Enrolment;
use backend\models\UserForm;
use common\models\Lesson;
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
use common\models\InvoiceLineItem;
use common\models\ItemType;
use common\models\TaxStatus;
use common\models\PaymentMethod;
use yii\web\ForbiddenHttpException;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller {

	public function behaviors() {
		return [
			'verbs' => [
				'class' => VerbFilter::className(),
				'actions' => [
					'delete' => ['post'],
				],
			],
		];
	}

	public function actions() {
		return [
			'upload' => [
				'class' => 'common\actions\UserImportUploadAction',
				'multiple' => false,
				'disableCsrf' => true,
				'responseFormat' => \yii\web\Response::FORMAT_JSON,
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
				'on afterSave' => function($event) {
					/* @var $file \League\Flysystem\File */
					// do something (resize, add watermark etc)
				}
			]
		];
	}

	/**
	 * Lists all User models.
	 * @return mixed
	 */
	public function actionIndex() {
		$searchModel = new UserSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
		return $this->render('index', [
					'searchModel' => $searchModel,
					'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a single User model.
	 * @param integer $id
	 * @return mixed
	 */
	public function actionView($id) {
		$session = Yii::$app->session;
		$location_id = $session->get('location_id');
		
		$searchModel = new UserSearch();
		$db = $searchModel->search(Yii::$app->request->queryParams);        
        
		$dataProvider = new ActiveDataProvider([
			'query' => Student::find()->where(['customer_id' => $id])
		]);

		$query = Student::find()
				->joinWith(['enrolment e' => function($query) use($id){
                    $query->joinWith('lessons')
						->where(['teacher_id' => $id])
						->groupBy('teacher_id');
				}])
				->where(['e.location_id' => $location_id]);
		$studentDataProvider = new ActiveDataProvider([
			'query' => $query,
		]);
		
		$query = Location::find()
				->joinWith('userLocations')
				->where(['user_id' => $id]);
		$locationDataProvider = new ActiveDataProvider([
			'query' => $query,
		]);
				
		$model = $this->findModel($id);

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
        ]
    ],
			]);
		$phoneDataProvider = new ActiveDataProvider([
			'query' => $model->getPhoneNumbers(),
            'sort' => [
            'defaultOrder' => [
            'is_primary' => SORT_DESC,
             ]
                ]
			]);
		

		$currentDate = new \DateTime();
		$lessonQuery = Lesson::find()
				->location($location_id)
				->student($id);

		$lessonDataProvider = new ActiveDataProvider([
			'query' => $lessonQuery,
		]);
		
		$enrolmentQuery = Enrolment::find()
			->joinWith('student s')
			->where(['location_id' => $location_id,'s.customer_id' => $id]);
		
		$enrolmentDataProvider = new ActiveDataProvider([
			'query' => $enrolmentQuery,
		]);
		
		$invoiceQuery = Invoice::find()
				->location($location_id)
				->student($id)
                ->where([
					'invoice.type' => Invoice::TYPE_INVOICE
				]);
				
		$invoiceDataProvider = new ActiveDataProvider([
			'query' => $invoiceQuery,
		]);

		$proFormaInvoiceQuery = Invoice::find()
				->location($location_id)
				->student($id)
                ->where([
					'invoice.type' => Invoice::TYPE_PRO_FORMA_INVOICE
				]);
				
		$proFormaInvoiceDataProvider = new ActiveDataProvider([
			'query' => $proFormaInvoiceQuery,
		]);
		
		$paymentDataProvider = new ActiveDataProvider([
			'query' => payment::find()
				->where(['user_id' => $model->id])
		]);
 		$paymentModel = new Payment();
		if ($paymentModel->load(Yii::$app->request->post())) {
			$paymentModel->user_id = $model->id;
			$paymentModel->payment_method_id = PaymentMethod::TYPE_ACCOUNT_ENTRY;
			if($paymentModel->amount < 0){
				$paymentModel->amount = abs($paymentModel->amount);
			}else{
				$paymentModel->amount = -abs($paymentModel->amount);
			}
			$date = \DateTime::createFromFormat('d-m-Y', $paymentModel->date);
    		$paymentModel->date = $date->format('Y-m-d H:i:s');
			$paymentModel->save();
			Yii::$app->session->setFlash('alert', [
				'options' => ['class' => 'alert-success'],
				'body' => 'Opening balance has been recorded successfully'
			]);
			return $this->redirect(['view', 'UserSearch[role_name]' => $searchModel->role_name, 'id' => $model->id, '#' => 'opening-balance']);
		}

		$openingBalancePaymentModel = Payment::find()
				->where([
					'user_id' => $model->id,
					'payment_method_id' => [PaymentMethod::TYPE_ACCOUNT_ENTRY, ],
			])->one();
	
		$remainingOpeningBalance = 0;
		if(! empty($openingBalancePaymentModel->id)){
			$openingBalanceCreditsUsed = Payment::find()
					->joinWith(['invoicePayment ip' => function($query) use($model){
						$query->where(['ip.invoice_id' => Payment::TYPE_OPENING_BALANCE_CREDIT]);	
					}])
					->where(['user_id' => $model->id])
					->sum('amount');

			$remainingOpeningBalance = $openingBalancePaymentModel->amount + $openingBalanceCreditsUsed;
		}
		$openingBalanceQuery = Payment::find()
				->joinWith(['invoicePayment ip' => function($query) use($model){
					$query->where(['ip.invoice_id' => Payment::TYPE_OPENING_BALANCE_CREDIT]);	
				}])
				->where(['user_id' => $model->id]);
		$openingBalanceDataProvider = new ActiveDataProvider([
			'query' => $openingBalanceQuery, 
		]);
	
		$query = Lesson::find()->alias('l')
					->location($location_id)
					->student($model->id)
					->unInvoiced()
					->completed();
		$unInvoicedLessonsDataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$invoiceLineItemModel = new InvoiceLineItem();
		if ($invoiceLineItemModel->load(Yii::$app->request->post())) {
			$lastInvoice = Invoice::lastInvoice($location_id);

			if (empty($lastInvoice)) {
				$invoiceNumber = 1;
			} else {
				$invoiceNumber = $lastInvoice->invoice_number + 1;
			}
			$invoice->user_id = $model->id;
			$invoice->invoice_number = $invoiceNumber;
			$invoice->type = Invoice::TYPE_INVOICE;
			$invoice->status = Invoice::STATUS_OWING;
			$invoice->date = (new \DateTime())->format('Y-m-d');
			$invoice->save();
			
			$invoiceLineItemModel->item_id = Invoice::ITEM_TYPE_MISC; 
			$invoiceLineItemModel->invoice_id = $invoice->id; 
			$invoiceLineItemModel->item_type_id = ItemType::TYPE_MISC;
			$taxStatus = TaxStatus::findOne(['id' => $invoiceLineItemModel->tax_status]);
			$invoiceLineItemModel->tax_status = $taxStatus->name;
			$invoiceLineItemModel->save();

			$invoice = Invoice::findOne(['id' => $invoice->id]);
			$invoice->subTotal += $invoiceLineItemModel->amount;
			$invoice->tax += $invoiceLineItemModel->tax_rate;
			$invoice->total = $invoice->subTotal + $invoice->tax ;
			$invoice->save();

			Yii::$app->session->setFlash('alert', [
				'options' => ['class' => 'alert-success'],
				'body' => 'Misc has been added successfully'
			]);
			return $this->redirect(['invoice/view','id' => $invoice->id, '#' => 'invoice']);
		}
		return $this->render('view', [
			'student' => new Student(),
			'dataProvider' => $dataProvider,
			'teacherDataProvider' => $teacherDataProvider,
			'model' => $model,
			'searchModel' => $searchModel,
			'program' => $program,
			'addressDataProvider' => $addressDataProvider,
			'phoneDataProvider' => $phoneDataProvider,
			'lessonDataProvider' => $lessonDataProvider,
			'locationDataProvider' => $locationDataProvider,
			'enrolmentDataProvider' => $enrolmentDataProvider,
			'invoiceDataProvider' => $invoiceDataProvider,
			'studentDataProvider' => $studentDataProvider,
			'paymentDataProvider' => $paymentDataProvider,
			'openingBalancePaymentModel' => $openingBalancePaymentModel,
			'openingBalanceDataProvider' => $openingBalanceDataProvider,
			'remainingOpeningBalance' => $remainingOpeningBalance,
			'unInvoicedLessonsDataProvider' => $unInvoicedLessonsDataProvider,
			'proFormaInvoiceDataProvider' => $proFormaInvoiceDataProvider,
		]);
	}

	/**
	 * Creates a new User model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 * @return mixed
	 */
	public function actionCreate() {
		$session = Yii::$app->session;
		$locationId = $session->get('location_id');

		$model = new UserForm();
		$addressModels = [new Address];
		$phoneNumberModels = [new PhoneNumber];
		$availabilityModels = [new TeacherAvailability];
		
		$model->setScenario('create');
		$model->roles = Yii::$app->request->queryParams['User']['role_name'];
		if ($model->roles === User::ROLE_STAFFMEMBER) {
			if (!Yii::$app->user->can('createStaff')) {
				throw new ForbiddenHttpException;
			}
		}
		if ($model->load(Yii::$app->request->post())) {
			$addressModels = UserForm::createMultiple(Address::classname());
			Model::loadMultiple($addressModels, Yii::$app->request->post());

			$phoneNumberModels = UserForm::createMultiple(PhoneNumber::classname());
			Model::loadMultiple($phoneNumberModels, Yii::$app->request->post());

			$availabilityModels = UserForm::createMultiple(TeacherAvailability::classname());
			Model::loadMultiple($availabilityModels, Yii::$app->request->post());

			$valid = $model->validate();
			$valid = (Model::validateMultiple($addressModels) || Model::validateMultiple($phoneNumberModels) || Model::validateMultiple($availabilityModels)) && $valid;

			if ($valid) {
				$transaction = \Yii::$app->db->beginTransaction();

				try {
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
						$userLocationModel = UserLocation::findOne([
							'user_id' => $model->getModel()->id,
							'location_id' => $locationId,
						]);
						foreach ($availabilityModels as $availabilityModel) {
							$availabilityModel->teacher_location_id = $userLocationModel->id;
							$fromTime = \DateTime::createFromFormat('H:i A', $availabilityModel->from_time);
							$toTime = \DateTime::createFromFormat('H:i A', $availabilityModel->to_time);
							$availabilityModel->from_time = $fromTime->format('H:i:s');
							$availabilityModel->to_time = $toTime->format('H:i:s');
							if (!($flag = $availabilityModel->save(false))) {
								$transaction->rollBack();
								break;
							}
						}
					}

					if ($flag) {
						$transaction->commit();
						Yii::$app->session->setFlash('alert', [
								'options' => ['class' => 'alert-success'],
								'body' => ucwords($model->roles) . ' profile has been created successfully'
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
					'programs' => ArrayHelper::map(Program::find()->active()->all(), 'id', 'name'),
					'availabilityModels' => (empty($availabilityModels)) ? [new TeacherAvailability] : $availabilityModels,
					'addressModels' => (empty($addressModels)) ? [new Address] : $addressModels,
					'phoneNumberModels' => (empty($phoneNumberModels)) ? [new PhoneNumber] : $phoneNumberModels,
					'locations' => ArrayHelper::map(Location::find()->all(), 'id', 'name'),
		]);
	}

	/**
	 * Updates an existing User model.
	 * @param integer $id
	 * @return mixed
	 */
	public function actionUpdate($id) {
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
				throw new ForbiddenHttpException;
			}
			if (($role === User::ROLE_CUSTOMER) && (!Yii::$app->user->can('updateCustomerProfile'))) {
				throw new ForbiddenHttpException;
			}
			if (($role === User::ROLE_OWNER) && (!Yii::$app->user->can('updateOwnerProfile'))) {
				throw new ForbiddenHttpException;
			}
			if (($role === User::ROLE_STAFFMEMBER) && (!Yii::$app->user->can('updateStaffProfile'))) {
				throw new ForbiddenHttpException;
			}
		}
		
		$addressModels = $model->addresses;
		$phoneNumberModels = $model->phoneNumbers;
		$availabilityModels = $model->availabilities;
		
		if ($model->load(Yii::$app->request->post())) {
			$oldAddressIDs = ArrayHelper::map($addressModels, 'id', 'id');
			$addressModels = UserForm::createMultiple(Address::classname(), $addressModels);
			Model::loadMultiple($addressModels, Yii::$app->request->post());
			$deletedAddressIDs = array_diff($oldAddressIDs, array_filter(ArrayHelper::map($addressModels, 'id', 'id')));

			$oldPhoneIDs = ArrayHelper::map($phoneNumberModels, 'id', 'id');
			$phoneNumberModels = UserForm::createMultiple(PhoneNumber::classname(), $phoneNumberModels);
			Model::loadMultiple($phoneNumberModels, Yii::$app->request->post());
			$deletedPhoneIDs = array_diff($oldPhoneIDs, array_filter(ArrayHelper::map($phoneNumberModels, 'id', 'id')));
			
			$oldAvailabilityIDs = ArrayHelper::map($availabilityModels, 'id', 'id');
			$availabilityModels = UserForm::createMultiple(TeacherAvailability::classname(), $availabilityModels);
			Model::loadMultiple($availabilityModels, Yii::$app->request->post());
			$deletedAvailabilityIDs = array_diff($oldAvailabilityIDs, array_filter(ArrayHelper::map($availabilityModels, 'id', 'id')));

			$valid = $model->validate();
			$valid = (Model::validateMultiple($addressModels) || Model::validateMultiple($phoneNumberModels) || Model::validateMultiple($availabilityModels)) && $valid;

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
						if (!empty($deletedAvailabilityIDs)) {
							TeacherAvailability::deleteAll(['id' => $deletedAvailabilityIDs]);
						}

						$userLocationModel = UserLocation::findOne([
							'user_id' => $id,
							'location_id' => $locationId,
						]);
						foreach ($availabilityModels as $availabilityModel) {
							$availabilityModel->teacher_location_id = $userLocationModel->id;
							$fromTime = \DateTime::createFromFormat('H:i A', $availabilityModel->from_time);
							$toTime = \DateTime::createFromFormat('H:i A', $availabilityModel->to_time);
							$availabilityModel->from_time = $fromTime->format('H:i:s');
							$availabilityModel->to_time = $toTime->format('H:i:s');
							if (!($flag = $availabilityModel->save(false))) {
								$transaction->rollBack();
								break;
							}
						}
					}
					if ($flag) {
						$transaction->commit();
						Yii::$app->session->setFlash('alert', [
								'options' => ['class' => 'alert-success'],
								'body' => ucwords($model->roles) . ' profile has been updated successfully'
						]);
					return $this->redirect(['view', 'UserSearch[role_name]' => $model->roles, 'id' => $model->getModel()->id]);
					}
				} catch (Exception $e) {
					$transaction->rollBack();
				}
			}


			
		}
	
		return $this->render('update', [
					'model' => $model,
					'roles' => ArrayHelper::map(Yii::$app->authManager->getRoles(), 'name', 'name'),
					'programs' => ArrayHelper::map(Program::find()->active()->all(), 'id', 'name'),
					'locations' => ArrayHelper::map(Location::find()->all(), 'id', 'name'),
					'availabilityModels' => (empty($availabilityModels)) ? [new TeacherAvailability] : $availabilityModels,
					'addressModels' => (empty($addressModels)) ? [new Address] : $addressModels,
					'phoneNumberModels' => (empty($phoneNumberModels)) ? [new PhoneNumber] : $phoneNumberModels,
		]);
	}

	/**
	 * Updates an existing User model.
	 * @param integer $id
	 * @return mixed
	 */
	public function actionImport() {
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
	 * @param integer $id
	 * @return mixed
	 */
	public function actionDelete($id) {
		$model = new UserForm();
		$model->setModel($this->findModel($id));

		$role = $model->roles;
		if (($role === User::ROLE_TEACHER) && (!Yii::$app->user->can('deleteTeacherProfile'))) {
			throw new ForbiddenHttpException;
		}
		if (($role === User::ROLE_CUSTOMER) && (!Yii::$app->user->can('deleteCustomerProfile'))) {
			throw new ForbiddenHttpException;
		}
		if (($role === User::ROLE_OWNER) && (!Yii::$app->user->can('deleteOwnerProfile'))) {
			throw new ForbiddenHttpException;
		}
		if (($role === User::ROLE_STAFFMEMBER) && (!Yii::$app->user->can('deleteStaffProfile'))) {
			throw new ForbiddenHttpException;
		}
	
		$db = Yii::$app->db;
		$command = $db->createCommand("DELETE u, up, pn, ua, a,ul,raa  FROM `user` u
			LEFT JOIN `user_profile` up ON u.`id` = up.`user_id`
			LEFT JOIN `phone_number` pn ON u.`id` = pn.`user_id`
			LEFT JOIN `user_address` ua ON u.`id` = ua.`user_id` 
			LEFT JOIN `user_location` ul ON ul.`user_id` = u.`id`           
			LEFT JOIN `address` a ON a.`id` = ua.`address_id` 
			LEFT JOIN `rbac_auth_assignment` raa ON raa.`user_id` = u.`id`  
			WHERE u.`id` = :id",[':id' => $id]);
		$command->execute();
		
		
		Yii::$app->session->setFlash('alert', [
			'options' => ['class' => 'alert-success'],
			'body' => ucwords($model->roles) . ' profile has been deleted successfully'
		]);
		return $this->redirect(['index', 'UserSearch[role_name]' => $model->roles]);
	}


	/**
	 * Deletes an existing User model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 * @param integer $id
	 * @return mixed
	 */
	public function actionDeleteAllCustomer() {
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
			'body' => Yii::t('backend', 'All customer and student records have been deleted successfully ', [])
		]);
		return $this->redirect(['index', 'UserSearch[role_name]' => User::ROLE_CUSTOMER]);
	}
    
    public function actionDeleteAllStaffMembers() {
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
			'body' => Yii::t('backend', 'All staffmembers records have been deleted successfully ', [])
		]);
		return $this->redirect(['index', 'UserSearch[role_name]' => User::ROLE_STAFFMEMBER]);
	}

	/**
	 * Finds the User model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 * @param integer $id
	 * @return User the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id) {
		$session = Yii::$app->session;
		$locationId = $session->get('location_id');
		$roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
			foreach ($roles as $name => $description) {
			$role = $name;
		}
		$adminModel = User::findOne(['id' => $id]); 
		$model = User::find()->joinWith(['location' => function($query) use($locationId) {
						$query->where(['location_id' => $locationId]);
					}])->where(['user.id' => $id])->one();
				if ($model !== null) {
					return $model;
				} 
				elseif($role === User::ROLE_ADMINISTRATOR &&  $adminModel != null){
					return $adminModel;
				}
				else {
					throw new NotFoundHttpException('The requested page does not exist.');
				}
			}
}
		
