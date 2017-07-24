<?php

namespace backend\controllers;

use Yii;
use common\models\InvoiceLineItemDiscount;
use common\models\TaxCode;
use common\models\Payment;
use common\models\PaymentMethod;
use common\models\InvoiceLineItem;
use yii\web\Response;
use yii\filters\ContentNegotiator;
use yii\bootstrap\ActiveForm;
use common\models\Invoice;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use common\models\User;
use common\models\InvoiceLog;
use yii\helpers\Json;

/**
 * InvoiceController implements the CRUD actions for Invoice model.
 */
class InvoiceLineItemController extends Controller
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
                'only' => ['edit', 'apply-discount', 'update', 'compute-net-price'],
                'formatParam' => '_format',
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
        ];
    }

	public function actionComputeTax()
    {
        $data = Yii::$app->request->rawBody;
        $data = Json::decode($data, true);
        $rate = $data['amount'] * ($data['tax'] / 100);

        return $rate;
    }
    
    public function actionUpdate($id) 
    {
        $model = $this->findModel($id);
        $lineItemDiscount = $model->lineItemDiscount;
        $customerDiscount = $model->customerDiscount;
        $paymentFrequencyDiscount = $model->enrolmentPaymentFrequencyDiscount;
        if (!$customerDiscount) {
            $customerDiscount = new InvoiceLineItemDiscount();
        }
        if (!$paymentFrequencyDiscount) {
            $paymentFrequencyDiscount = new InvoiceLineItemDiscount();
        }
        if (!$lineItemDiscount) {
            $lineItemDiscount = new InvoiceLineItemDiscount();
        }
        $customerDiscount->setScenario(InvoiceLineItemDiscount::SCENARIO_ON_INVOICE);
        $paymentFrequencyDiscount->setScenario(InvoiceLineItemDiscount::SCENARIO_ON_INVOICE);
        $lineItemDiscount->setScenario(InvoiceLineItemDiscount::SCENARIO_ON_INVOICE);
        
        $model->setScenario(InvoiceLineItem::SCENARIO_EDIT);
        $model->tax_status = $model->taxStatus;
        $data = $this->renderAjax('/invoice/line-item/_form', [
            'model' => $model,
            'customerDiscount' => $customerDiscount,
            'paymentFrequencyDiscount' => $paymentFrequencyDiscount,
            'lineItemDiscount' => $lineItemDiscount
        ]);
        $post = Yii::$app->request->post();
        if ($model->load($post)) {
            $customerDiscount->load($post['CustomerDiscount'], '');
            if ($customerDiscount->isNewRecord) {
                $customerDiscount->invoiceLineItemId = $id;
                $customerDiscount->valueType = InvoiceLineItemDiscount::VALUE_TYPE_PERCENTAGE;
                $customerDiscount->type = InvoiceLineItemDiscount::TYPE_CUSTOMER;
            }
            $lineItemDiscount->load($post['LineItemDiscount'], '');
            if ($lineItemDiscount->isNewRecord) {
                $lineItemDiscount->invoiceLineItemId = $id;
                $lineItemDiscount->type = InvoiceLineItemDiscount::TYPE_CUSTOMER;
            }
            $paymentFrequencyDiscount->load($post['PaymentFrequencyDiscount'], '');
            if ($paymentFrequencyDiscount->isNewRecord) {
                $paymentFrequencyDiscount->valueType = InvoiceLineItemDiscount::VALUE_TYPE_PERCENTAGE;
                $paymentFrequencyDiscount->invoiceLineItemId = $id;
                $paymentFrequencyDiscount->type = InvoiceLineItemDiscount::TYPE_ENROLMENT_PAYMENT_FREQUENCY;
            }
            if ($customerDiscount->canSave()) {
                if (empty($customerDiscount->value)) {
                    $customerDiscount->value = 0.0;
                }
                $customerDiscount->save();
            }
            if ($paymentFrequencyDiscount->canSave()) {
                if (empty($paymentFrequencyDiscount->value)) {
                    $paymentFrequencyDiscount->value = 0.0;
                }
                $paymentFrequencyDiscount->save();
            }
            if ($lineItemDiscount->canSave()) {
                if (empty($lineItemDiscount->value)) {
                    $lineItemDiscount->value = 0.0;
                }
                $lineItemDiscount->save();
            }
            $taxStatus         = $post['InvoiceLineItem']['tax_status'];
            $taxCode           = $model->computeTaxCode($taxStatus);
            $model->tax_status = $taxCode->taxStatus->name;
            $model->tax_type   = $taxCode->taxType->name;
            if($model->save()) {
                $response = [
                    'status' => true,
		];	
            } else {
                $response = [
                    'status' => false,
                    'errors' => ActiveForm::validate($model),
                ];	
            }
            return $response;
        } else {

            return [
                'status' => true,
                'message' => 'Warning: You have entered a non-approved Arcadia '
                    . 'discount.All non-approved discounts must be submitted in '
                    . 'writing and approved by Head Office prior to entering a discount, '
                    . 'otherwise you are in breach of your agreement.',
                'data' => $data,
            ];
        }
    }

    public function actionEditOpeningBalance($model, $newAmount)
    {
        $model->setScenario(InvoiceLineItem::SCENARIO_OPENING_BALANCE);
        $model->amount = $newAmount;
        $model->save();
        $payments = $model->invoice->payments;
        if($newAmount < 0) {
            $model->invoice->subTotal = 0.00;
            $model->invoice->total = $model->invoice->subTotal;
            $model->invoice->save();
            if(!empty($payments)) {
                foreach ($payments as $payment){
                    if($payment->isAccountEntry()) {
                        $payment->amount = abs($newAmount);
                        $payment->save();
                        continue;
                    }
                }
            }else {
                $payment = new Payment();
                $payment->amount = abs($newAmount);
                $payment->invoiceId = $payments = $model->invoice->id;
                $payment->payment_method_id = PaymentMethod::TYPE_ACCOUNT_ENTRY;
                $payment->reference = null;
                $payment->save();
            }
        }elseif($newAmount > 0) {
            $model->invoice->subTotal = $model->invoice->lineItemTotal;
            $model->invoice->total = $model->invoice->subTotal;
            $model->invoice->save();
            if(!empty($payments)) {
                foreach ($payments as $payment){
                    if($payment->isAccountEntry()) {
                        $payment->delete();
                    }
                }
            }
        }
        
        $result = [
            'output' => $newAmount,
            'message' => '',
        ];

        return $result;
    }

    public function actionEditOtherItems($model, $newAmount)
    {
        $model->amount = $newAmount;
        if($model->save()) {
			$model->trigger(InvoiceLineItem::EVENT_EDIT);
		}
        $result = [
            'output' => $newAmount,
            'message' => '',
        ];

        return $result;
    }

    public function actionApplyDiscount($id)
    {
        $invoiceModel = Invoice::findOne($id);
        $invoiceModel->setScenario(Invoice::SCENARIO_DISCOUNT);
        if ($invoiceModel->load(Yii::$app->request->post())) {
            if ($invoiceModel->validate()) {
                $invoiceLineItems = $invoiceModel->lineItems;
                foreach ($invoiceLineItems as $invoiceLineItem) {
                    $invoiceLineItem->discount = $invoiceModel->discountApplied;
                    $invoiceLineItem->discountType = InvoiceLineItem::DISCOUNT_PERCENTAGE;
                    $invoiceLineItem->save();
                }
                $invoiceModel->save();
                $response = [
                    'status' => true,
                    'invoiceStatus' => $invoiceModel->getStatus(),
					'amount' => round($invoiceModel->invoiceBalance,2)
                ];
            } else {
                $errors = ActiveForm::validate($invoiceModel);
                $response = [
                    'status' => false,
                    'errors' => $errors,
                ];
            }

            return $response;
        }

    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
		$model->on(InvoiceLineItem::EVENT_DELETE, [new InvoiceLog(), 'deleteLineItem']);
		$user = User::findOne(['id' => Yii::$app->user->id]);
		$model->userName = $user->publicIdentity;
        $invoiceModel = $model->invoice;
        if($model->delete()) {
        	$invoiceModel->save();
			$model->trigger(InvoiceLineItem::EVENT_DELETE);
		}
        Yii::$app->session->setFlash('alert', [
                'options' => ['class' => 'alert-success'],
                'body' => 'Line Item has been deleted successfully',
            ]);

        return $this->redirect(['invoice/view', 'id' => $model->invoice->id]);
    }

    protected function findModel($id)
    {
        $session = Yii::$app->session;
        $locationId = $session->get('location_id');
        $model = InvoiceLineItem::find()
                ->joinWith(['invoice' => function ($query) use ($locationId) {
                    $query->where(['location_id' => $locationId]);
                }])
                ->where([
                    'invoice_line_item.id' => $id,
                ])
                ->one();
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    public function actionComputeNetPrice($id)
    {
        $rawData = Yii::$app->request->rawBody;
        $data = Json::decode($rawData, true);
        $invoiceLineItem = InvoiceLineItem::findOne($id);
        $invoiceLineItem->load($data, '');
        $taxCode           = $invoiceLineItem->computeTaxCode($data['taxStatus']);
        $invoiceLineItem->tax_status = $taxCode->taxStatus->name;
        $invoiceLineItem->tax_type   = $taxCode->taxType->name;
        $invoiceLineItem->tax_rate   = $invoiceLineItem->amount * $invoiceLineItem->taxType->taxCode->rate / 100;
        return [
            'netPrice' => $invoiceLineItem->netPrice,
            'taxRate' => $invoiceLineItem->tax_rate,
            'taxPercentage' => $invoiceLineItem->taxType->taxCode->rate
        ];
    }
}
