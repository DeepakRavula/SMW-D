<?php

namespace backend\controllers;

use Yii;
use common\models\Payment;
use backend\models\search\PaymentSearch;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\Invoice;
use common\models\PaymentMethod;
use yii\widgets\ActiveForm;
use yii\web\Response;
use common\models\CreditUsage;
use yii\filters\ContentNegotiator;
use common\models\User;
use common\models\timelineEvent\TimelineEventPayment;
/**
 * PaymentsController implements the CRUD actions for Payments model.
 */
class PaymentController extends Controller
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
                'only' => ['edit', 'invoice-payment', 'credit-payment', 'update', 'delete'],
                'formatParam' => '_format',
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
        ];
    }

    /**
     * Lists all Payments models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PaymentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Payments model.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Payments model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Payment();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Payments model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->date = (new \DateTime($model->date))->format('d-m-Y');
        $data = $this->renderAjax('/invoice/payment/_form', [
            'model' => $model,
        ]);
      	
		return [
			'status' => true,
			'data' => $data,
		];
    }

    /**
     * Deletes an existing Payments model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model        = $this->findModel($id);
		$userModel = User::findOne(['id' => Yii::$app->user->id]);
        $model->on(Payment::EVENT_DELETE, [new TimelineEventPayment(), 'deletePayment']);
		$model->userName = $userModel->publicIdentity;
        $modelInvoice = $model->invoice;
        if ($model->isCreditApplied()) {
            $creditUsedPaymentModel        = $model->creditUsage->debitUsagePayment;
            $creditUsedPaymentInvoiceModel = $model->creditUsage->debitUsagePayment->invoice;
            $creditUsedPaymentModel->delete();
            $creditUsedPaymentInvoiceModel->save();
            $model->creditUsage->delete();
        } elseif ($model->isCreditUsed()) {
            $creditAppliedPaymentInvoiceModel = $model->debitUsage->creditUsagePayment->invoice;
            $creditAppliedPaymentModel = $model->debitUsage->creditUsagePayment;
            $creditAppliedPaymentModel->delete();
            $creditAppliedPaymentInvoiceModel->save();
            $model->debitUsage->delete();
        } elseif ($model->isAccountEntry()) {
            $modelInvoice->lineItem->delete();
        }
            $model->delete();
        	$modelInvoice->save();
			$model->trigger(Payment::EVENT_DELETE);
		
		return [
			'status' => true,
		];
    }

    /**
     * Finds the Payments model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param string $id
     *
     * @return Payments the loaded model
     *
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Payment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionPrint()
    {
        $searchModel = new PaymentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        $this->layout = '/print';

        return $this->render('/report/payment/_print', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionInvoicePayment($id)
    {
        $paymentModel = new Payment();
		$userModel = User::findOne(['id' => Yii::$app->user->id]);
        $paymentModel->on(Payment::EVENT_CREATE, [new TimelineEventPayment(), 'create']);
		$paymentModel->userName = $userModel->publicIdentity;
        $db = \Yii::$app->db;
        $transaction = $db->beginTransaction();
        $request = Yii::$app->request;
        if ($paymentModel->load($request->post())) {
            $paymentModel->date = (new \DateTime($paymentModel->date))->format('Y-m-d H:i:s');
            $paymentModel->invoiceId = $id;
            if($paymentModel->save()) {
            	$transaction->commit();
				return [
					'status' => true,
				];	
			} else {
				$errors = ActiveForm::validate($paymentModel); 
			return [
				'status' => false,
				'errors' => $errors,
			];
		}
        } 
    }

    public function actionCreditPayment($id)
    {
        $model = Invoice::findOne(['id' => $id]);
        $paymentModel = new Payment();
		$userModel = User::findOne(['id' => Yii::$app->user->id]);
        $paymentModel->on(Payment::EVENT_CREATE, [new TimelineEventPayment(), 'create']);
		$paymentModel->userName = $userModel->publicIdentity;
        $paymentModel->setScenario('apply-credit');
        $request = Yii::$app->request;
        if ($paymentModel->load($request->post())) {
            $paymentModel->payment_method_id = PaymentMethod::TYPE_CREDIT_APPLIED;
            $paymentModel->reference = $paymentModel->sourceId;
            $paymentModel->invoiceId = $model->id;
            if ($paymentModel->validate()) {
                $paymentModel->save();

                $creditPaymentId = $paymentModel->id;
                $paymentModel->id = null;
                $paymentModel->isNewRecord = true;
                $paymentModel->payment_method_id = PaymentMethod::TYPE_CREDIT_USED;
                $paymentModel->invoiceId = $paymentModel->sourceId;
                $paymentModel->reference = $model->id;
                $paymentModel->save();

                $debitPaymentId = $paymentModel->id;
                $creditUsageModel = new CreditUsage();
                $creditUsageModel->credit_payment_id = $creditPaymentId;
                $creditUsageModel->debit_payment_id = $debitPaymentId;
                $creditUsageModel->save();

                $invoiceModel = Invoice::findOne(['id' => $paymentModel->sourceId]);
                $invoiceModel->balance = $invoiceModel->balance + abs($paymentModel->amount);
                $invoiceModel->save();
                $response = [
                    'status' => true,
                ];
            } else {
                $paymentModel = ActiveForm::validate($paymentModel);
                $response = [
                    'status' => false,
                    'errors' => $paymentModel,
                ];
            }

            return $response;
        }
    }

    public function actionEdit($id)
    {
        $model = Payment::findOne(['id' => $id]);
		$userModel = User::findOne(['id' => Yii::$app->user->id]);
        $model->on(Payment::EVENT_EDIT, [new TimelineEventPayment(), 'editPayment'], ['oldAttributes' => $model->getOldAttributes()]);
        $model->userName = $userModel->publicIdentity;
        $request = Yii::$app->request;
        if ($model->load($request->post())) {
            $model->date = (new \DateTime($model->date))->format('Y-m-d H:i:s');
			if ($model->isAccountEntry()) {
				$model->setScenario(Payment::SCENARIO_OPENING_BALANCE);
				$response = Yii::$app->runAction('payment/edit-account-entry',
					['model' => $model, 'newAmount' => $model->amount]);
			} 
			if ($model->isOtherPayments()) {
				$response = Yii::$app->runAction('payment/edit-other-payments',
					['model' => $model, 'newAmount' => $model->amount]);
			}
			if ($model->isCreditApplied()) {
				$response = Yii::$app->runAction('payment/edit-credit-applied',
					['model' => $model, 'newAmount' => $model->amount]);
			}
			if ($model->isCreditUsed()) {
				$model->setScenario(Payment::SCENARIO_CREDIT_USED);
				$response = Yii::$app->runAction('payment/edit-credit-used',
					['model' => $model, 'newAmount' => $model->amount]);
			}
			return $response;
		}
    }

    public function actionEditOtherPayments($model, $newAmount)
    {
        $model->amount = $newAmount;
        $invoiceModel = $model->invoice;
        $invoiceModel->balance=$invoiceModel->total-abs($newAmount);
        $invoiceModel->save();
        $model->save();
        $result = [
            'status' => true,
            'amount' => $model->invoiceBalance,
        ];

        return $result;
    }

    public function actionEditAccountEntry($model, $newAmount)
    {
        $model->amount = $newAmount;
        $invoiceModel          = $model->invoice;
        $lineItemModel         = $model->invoice->lineItem;
        $lineItemModel->amount = -($model->amount);
        $model->save();
        if ($newAmount < 0) {
            $model->delete();
            $lineItemModel->amount = abs($newAmount);
            $invoiceModel->subTotal = $lineItemModel->amount;
            $invoiceModel->total    = $invoiceModel->subTotal + $invoiceModel->tax;
            $invoiceTotal= $invoiceModel->subTotal + $invoiceModel->tax;
            $invoiceModel->balance=$invoiceTotal-abs($newAmount);

        }
        $lineItemModel->save();
        $invoiceModel->save();

        $result = [
            'status' => true,
            'amount' => $model->invoiceBalance,
        ];

        return $result;
    }

    public function actionEditCreditApplied($model, $newAmount)
    {
        $model->setScenario(Payment::SCENARIO_CREDIT_APPLIED);
        $model->lastAmount = $model->amount;
        $model->amount = $newAmount;
        $model->differnce = $model->amount - $model->lastAmount;
        $model->invoiceBalance= $model->invoiceBalance + $model->difference;
        if ($model->validate()) {
            $model->save();

            $result = [
            	'status' => true,
		'amount' => $model->invoiceBalance,
            ];
        } else {
            $errors = ActiveForm::validate($model);
            $result = [
                'status' => false,
                'errors' => $errors['payment-amount'],
            ];
        }

        return $result;
    }

    public function actionEditCreditUsed($model, $newAmount)
    {
        $model->setScenario(Payment::SCENARIO_CREDIT_USED);
        $model->lastAmount = $model->amount;
        $model->amount = $newAmount;
        $model->differnce = $model->amount - $model->lastAmount;
        $model->invoiceBalance= $model->invoiceBalance + $model->difference;
        if ($model->validate()) {
            $model->save();

            $result = [
                'status' => true,
				'amount' => $model->invoiceBalance,
            ];
        } else {
            $errors = ActiveForm::validate($model);
            $result = [
                'status' => false,
                'errors' => $errors['payment-amount'],
            ];
        }

        return $result;
    }
}
