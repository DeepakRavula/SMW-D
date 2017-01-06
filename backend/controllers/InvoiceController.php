<?php

namespace backend\controllers;

use Yii;
use common\models\Invoice;
use common\models\InvoiceLineItem;
use backend\models\search\InvoiceSearch;
use backend\models\search\LessonSearch;
use common\models\User;
use common\models\UserProfile;
use common\models\Payment;
use common\models\Lesson;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\ItemType;
use common\models\TaxCode;
use common\models\Location;
use common\models\CreditUsage;
use common\models\PaymentMethod;
use yii\helpers\Json;
use yii\web\Response;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use common\models\Note;

/**
 * InvoiceController implements the CRUD actions for Invoice model.
 */
class InvoiceController extends Controller
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
     * Lists all Invoice models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new InvoiceSearch();
		$request = Yii::$app->request;
		$invoiceSearchRequest = $request->get('InvoiceSearch');
		if ((int) $invoiceSearchRequest['type'] === Invoice::TYPE_PRO_FORMA_INVOICE) {
			$currentDate				 = new \DateTime();
			$searchModel->toDate		 = $currentDate->format('d-m-Y');
			$fromDate					 = clone $currentDate;
			$fromDate		 = $fromDate->modify('-90 days');
			$searchModel->fromDate = $fromDate->format('d-m-Y');
			$searchModel->invoiceStatus	 = Invoice::STATUS_OWING;
			$searchModel->mailStatus	 = InvoiceSearch::STATUS_MAIL_NOT_SENT;
		}

		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Invoice model.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionBlankInvoice()
    {
        $invoice = new Invoice();
        $request = Yii::$app->request;
        $invoiceRequest = $request->get('Invoice');
        if (empty($invoiceRequest['customer_id'])) {
            $invoice->user_id = Invoice::USER_UNASSINGED;
            $invoice->type = Invoice::TYPE_INVOICE;
        }
        if (!empty($invoiceRequest['customer_id'])) {
            $invoice->user_id = $invoiceRequest['customer_id'];
            $invoice->type = $invoiceRequest['type'];
        }
        $location_id = Yii::$app->session->get('location_id');
        $invoice->location_id = $location_id;
        $invoice->save();

        return $this->redirect(['view', 'id' => $invoice->id]);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        $invoiceLineItems = InvoiceLineItem::find()->where(['invoice_id' => $id]);
        $invoiceLineItemsDataProvider = new ActiveDataProvider([
            'query' => $invoiceLineItems,
        ]);

        $request = Yii::$app->request;
        $invoiceRequest = $request->post('Invoice');
        $customerId = $invoiceRequest['customer_id'];
        if (!empty($model->user_id)) {
            $customer = User::findOne(['id' => $model->user_id]);
        }
        if (isset($customerId)) {
            $customer = User::findOne(['id' => $customerId]);
        }
        if (empty($customer)) {
            $customer = new User();
        }

        $customerInvoicePayments = Payment::find()
                ->joinWith(['invoicePayment ip' => function ($query) use ($model) {
                    $query->where(['ip.invoice_id' => $model->id]);
                }])
                ->where(['user_id' => $model->user_id]);

        $customerInvoicePaymentsDataProvider = new ActiveDataProvider([
            'query' => $customerInvoicePayments,
        ]);

        $invoicePayments = Payment::find()
                ->joinWith(['invoicePayment ip' => function ($query) use ($model) {
                    $query->where(['ip.invoice_id' => $model->id]);
                }])
                ->orderBy(['date' => SORT_DESC]);

        $invoicePaymentsDataProvider = new ActiveDataProvider([
            'query' => $invoicePayments,
        ]);

        if (!empty($model->user->userProfile->user_id)) {
            $userModel = UserProfile::findOne(['user_id' => $customer->id]);
        } else {
            $userModel = new UserProfile();
        }

        if ($request->isPost) {
            if (isset($_POST['customer-invoice'])) {
                if ($model->load($request->post())) {
                    $model->user_id = $customer->id;
                    $model->save();
                }
            }
            if (isset($_POST['guest-invoice'])) {
                if ($customer->load($request->post())) {
                    if ($customer->save()) {
                        $model->user_id = $customer->id;
                        $model->save();

                        if ($userModel->load($request->post())) {
                            $userModel->user_id = $customer->id;
                            $userModel->save();
                            $auth = Yii::$app->authManager;
                            if (empty($customer->id)) {
                                $auth->assign($auth->getRole(User::ROLE_GUEST), $customer->id);
                            }
                            Yii::$app->session->setFlash('alert', [
                                'options' => ['class' => 'alert-success'],
                                'body' => 'Invoice has been updated successfully',
                            ]);
                        }
                    }
                }
            }
        }
		$post = Yii::$app->request->post();
		if (isset($post['hasEditable'])) {
			$response = Yii::$app->response;
			$response->format = Response::FORMAT_JSON;
			if(! empty($post['notes'])) {
				$model->notes = $post['notes'];
				$model->save();
				return ['output' => $model->notes, 'message' => ''];
			}
		}

		$notes = Note::find()
			->where(['instanceId' => $model->id, 'instanceType' => Note::INSTANCE_TYPE_INVOICE])
			->orderBy(['createdOn' => SORT_DESC]);

        $noteDataProvider = new ActiveDataProvider([
            'query' => $notes,
        ]);

        return $this->render('view', [
			'model' => $model,
			'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
			'invoicePayments' => $customerInvoicePaymentsDataProvider,
			'customer' => empty($customer) ? new User() : $customer,
			'userModel' => $userModel,
			'invoicePaymentsDataProvider' => $invoicePaymentsDataProvider,
			'noteDataProvider' => $noteDataProvider
        ]);
    }

    public function actionAddMisc($id)
    {
        $response = \Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        $model = $this->findModel($id);
        $invoiceLineItemModel = new InvoiceLineItem();
        if ($invoiceLineItemModel->load(Yii::$app->request->post())) {
            $invoiceLineItemModel->item_id = Invoice::ITEM_TYPE_MISC;
            $invoiceLineItemModel->invoice_id = $model->id;
            $invoiceLineItemModel->item_type_id = ItemType::TYPE_MISC;
            $invoiceLineItemModel->discount = 0.0;
            if ($invoiceLineItemModel->validate()) {
                $invoiceLineItemModel->save();
                $model->save();
                $response = [
                    'invoiceStatus' => $model->getStatus(),
                    'status' => true,
                ];
            } else {
                $invoiceLineItemModel = ActiveForm::validate($invoiceLineItemModel);
                $response = [
                    'status' => false,
                    'errors' => $invoiceLineItemModel,
                ];
            }

            return $response;
        }
    }

    public function actionFetchSummaryAndStatus($id)
    {
        $response = \Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        $model = $this->findModel($id);
        $summary = $this->renderPartial('_view-bottom-summary', [
            'model' => $model,
        ]);
        $InvoicePaymentDetails = $this->renderPartial('_invoice-summary', [
            'model' => $model,
        ]);
        $status = $model->getStatus();

        return [
            'summary' => $summary,
            'status' => $status,
            'details' => $InvoicePaymentDetails,
        ];
    }

    public function actionComputeTax()
    {
        $response = \Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        $locationId = Yii::$app->session->get('location_id');
        $locationModel = Location::findOne(['id' => $locationId]);
        $today = (new \DateTime())->format('Y-m-d H:i:s');
        $data = Yii::$app->request->rawBody;
        $data = Json::decode($data, true);
        $taxCode = TaxCode::find()
            ->joinWith(['taxStatus' => function ($query) use ($data) {
                $query->where(['tax_status.id' => $data['taxStatusId']]);
            }])
            ->where(['<=', 'start_date', $today])
            ->andWhere(['province_id' => $locationModel->province_id])
            ->orderBy('start_date DESC')
            ->one();
        $rate = $data['amount'] * $taxCode->rate / 100;

        return [
            'tax_type' => $taxCode->taxType->name,
            'code' => $taxCode->code,
            'rate' => $rate,
            'tax_status' => $data['taxStatusName'],
        ];
    }

    /**
     * Creates a new Invoice model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $searchModel = new LessonSearch();
        $currentMonth = new \DateTime();
        $searchModel->fromDate = $currentMonth->format('15-m-Y');
        $currentMonth->add(new \DateInterval('P1M'));
        $searchModel->toDate = $currentMonth->format('15-m-Y');
        $params = Yii::$app->request->queryParams;
        if (!empty($params['Invoice']['customer_id'])) {
            $params['LessonSearch']['customerId'] = $params['Invoice']['customer_id'];
        }
        if (!empty($params['Invoice']['type'])) {
            $params['LessonSearch']['invoiceType'] = $params['Invoice']['type'];
        }
        $dataProvider = null;
        $invoice = new Invoice();
        $request = Yii::$app->request;
        $invoiceRequest = $request->get('Invoice');
        $invoice->type = $invoiceRequest['type'];
        $location_id = Yii::$app->session->get('location_id');
        if (isset($invoiceRequest['customer_id'])) {
            $customer = User::findOne(['id' => $invoiceRequest['customer_id']]);
            $dataProvider = $searchModel->search($params);

            if (empty($customer)) {
                throw new NotFoundHttpException('The requested page does not exist.');
            }

            $currentDate = new \DateTime();
            $invoice->customer_id = $customer->id;
            $searchModel->customerId = $customer->id;
            $searchModel->invoiceType = $invoice->type;
        }
        $post = $request->post();
        if ((!empty($post['selection'])) && is_array($post['selection']) && (!empty($customer->id))) {
            $invoice->type = $invoiceRequest['type'];
            $invoice->user_id = $customer->id;
            $invoice->location_id = $location_id;
            $invoice->notes = $post['Invoice']['notes'];
            $invoice->internal_notes = $post['Invoice']['internal_notes'];
            $invoice->save();
            foreach ($post['selection'] as $selection) {
                $lesson = Lesson::findOne(['id' => $selection]);
                $invoice->addLineItem($lesson);
            }
            $invoice->save();

            return $this->redirect(['view', 'id' => $invoice->id]);
        } else {
            return $this->render('create', [
                'model' => $invoice,
                'dataProvider' => $dataProvider,
                'customer' => (empty($customer)) ? [new User()] : $customer,
                'searchModel' => $searchModel,
            ]);
        }
    }

    /**
     * Updates an existing Invoice model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('alert', [
                'options' => ['class' => 'alert-success'],
                'body' => 'Invoice has been updated successfully',
            ]);

            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Invoice model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionDelete($id)
	{
		$model = $this->findModel($id);
		$response = \Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
		$model->setScenario(Invoice::SCENARIO_DELETE);
		if ($model->validate()) {
			InvoiceLineItem::deleteAll(['invoice_id' => $model->id]);
			$model->delete();
			$response = [
				'status' => true,
				'url' => Url::to(['index', 'InvoiceSearch[type]' => $model->type]),
			];
		} else {
			$model	 = ActiveForm::validate($model);
			$response		 = [
				'errors' => $model['invoice-id'],
			];
		}
		return $response;
	}

	/**
     * Finds the Invoice model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param int $id
     *
     * @return Invoice the loaded model
     *
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $session = Yii::$app->session;
        $locationId = $session->get('location_id');
        $model = Invoice::find()
                ->where([
                    'invoice.id' => $id,
                    'location_id' => $locationId,
                ])
                ->one();
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionAddInvoice()
    {
        $model = new User();

        if (isset(Yii::$app->request->queryParams['User'])) {
            $model->customer = Yii::$app->request->queryParams['User']['customer'];
        }

        return $this->render('create', [
                    'model' => $model,
        ]);
    }

    public function actionPrint($id)
    {
        $model = $this->findModel($id);
        $invoiceLineItems = InvoiceLineItem::find()->where(['invoice_id' => $id]);
        $invoiceLineItemsDataProvider = new ActiveDataProvider([
            'query' => $invoiceLineItems,
        ]);

        $this->layout = '/print';

        return $this->render('_print', [
                    'model' => $model,
                    'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
        ]);
    }

    public function actionUpdateMailStatus($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        $model->load($request->post());
        $model->save();
    }

    public function actionSendMail($id)
    {
        $model      = $this->findModel($id);
        $isMailSend = $model->sendEmail();
        if($isMailSend)
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

	public function actionAllCompletedLessons()
	{
		$locationId = Yii::$app->session->get('location_id');
		$lessons = Lesson::find()
			->location($locationId)
            ->unInvoiced()
			->completed()
			->all();
		foreach($lessons as $lesson) {
			$invoice = new Invoice();
			$invoice->type = Invoice::TYPE_INVOICE;
			$invoice->user_id = $lesson->course->enrolment->student->customer_id;
			$invoice->location_id = $locationId;
			$invoice->save();
			$invoice->addLineItem($lesson);
			$invoice->save();

			$proFormaInvoice      = Invoice::find()
                ->select(['invoice.id', 'SUM(payment.amount) as credit'])
                ->proFormaCredit($lesson->id)
                ->one();

            if (!empty($proFormaInvoice)) {
				$invoice->addPayment($proFormaInvoice);
            }
		}
		
        return $this->redirect(['index', 'InvoiceSearch[type]' => Invoice::TYPE_INVOICE]);
	}
}
