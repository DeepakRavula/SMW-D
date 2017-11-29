<?php

namespace backend\controllers;

use Yii;
use common\models\Payment;
use common\models\PaymentMethod;
use common\models\InvoiceLineItem;
use yii\web\Response;
use yii\filters\ContentNegotiator;
use yii\bootstrap\ActiveForm;
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
        $lineItemDiscount = $model->item->loadLineItemDiscount($id);
        $paymentFrequencyDiscount = $model->item->loadPaymentFrequencyDiscount($id);
        $customerDiscount = $model->item->loadCustomerDiscount($id);
        $multiEnrolmentDiscount = $model->item->loadMultiEnrolmentDiscount($id);
        if (!$model->isLessonItem() && !$model->isOpeningBalance()) {
            $model->tax_status = $model->taxStatus;
        }
        $model->setScenario(InvoiceLineItem::SCENARIO_EDIT);
        if ($model->invoice->isReversedInvoice()) {
            $model->setScenario(InvoiceLineItem::SCENARIO_NEGATIVE_VALUE_EDIT);
        }
        $data = $this->renderAjax('/invoice/line-item/_form', [
            'model' => $model,
            'customerDiscount' => $customerDiscount,
            'paymentFrequencyDiscount' => $paymentFrequencyDiscount,
            'lineItemDiscount' => $lineItemDiscount,
            'multiEnrolmentDiscount' => $multiEnrolmentDiscount
        ]);
        $post = Yii::$app->request->post();
        if ($model->load($post)) {
            if (!$model->isOpeningBalance()) {
                $customerDiscount->load($post);
                $lineItemDiscount->load($post);
                $customerDiscount->save();
                $lineItemDiscount->save();
                if (!$model->isLessonItem()) {
                    $taxStatus         = $post['InvoiceLineItem']['tax_status'];
                    $taxCode           = $model->computeTaxCode($taxStatus);
                    $model->tax_status = $taxCode->taxStatus->name;
                    $model->tax_type   = $taxCode->taxType->name;
                } else {
                    $paymentFrequencyDiscount->load($post);
                    $multiEnrolmentDiscount->load($post);
                    $paymentFrequencyDiscount->save();
                    $multiEnrolmentDiscount->save();
                }
            }
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

    public function actionApplyDiscount()
    {
        $lineItemIds = Yii::$app->request->get('InvoiceLineItem')['ids'];
        $isLineItemDiscountValueDiff = false;
        $isPaymentFrequencyDiscountValueDiff = false;
        $isCustomerDiscountValueDiff = false;
        $isMultiEnrolmentDiscountValueDiff = false;
        foreach ($lineItemIds as $key => $lineItemId) {
            $model = $this->findModel($lineItemId);
            $lineItemDiscount = $model->item->loadLineItemDiscount($lineItemId);
            $paymentFrequencyDiscount = $model->item->loadPaymentFrequencyDiscount($lineItemId);
            $customerDiscount = $model->item->loadCustomerDiscount($lineItemId);
            $multiEnrolmentDiscount = $model->item->loadMultiEnrolmentDiscount($lineItemId);
            if ($key === 0) {
                $lineItemDiscountValue = $lineItemDiscount ? $lineItemDiscount->value : null;
                $paymentFrequencyDiscountValue = $paymentFrequencyDiscount ? $paymentFrequencyDiscount->value : null;
                $customerDiscountValue = $customerDiscount ? $customerDiscount->value : null;
                $multiEnrolmentDiscountValue = $multiEnrolmentDiscount ? $multiEnrolmentDiscount->value : null;
            } else {
                if ((float) $lineItemDiscountValue !== (float) ($lineItemDiscount ? $lineItemDiscount->value : null)) {
                    $isLineItemDiscountValueDiff = true;
                }
                if ((float) $paymentFrequencyDiscountValue !== (float) ($paymentFrequencyDiscount ? $paymentFrequencyDiscount->value : null)) {
                    $isPaymentFrequencyDiscountValueDiff = true;
                }
                if ((float) $customerDiscountValue !== (float) ($customerDiscount ? $customerDiscount->value : null)) {
                    $isCustomerDiscountValueDiff = true;
                }
                if ((float) $multiEnrolmentDiscountValue !== (float) ($multiEnrolmentDiscount ? $multiEnrolmentDiscount->value : null)) {
                    $isMultiEnrolmentDiscountValueDiff = true;
                }
            }
        }
        $lineItemId = end($lineItemIds);
        $lineItemDiscount = $model->item->loadLineItemDiscount($lineItemId, $isLineItemDiscountValueDiff);
        $paymentFrequencyDiscount = $model->item->loadPaymentFrequencyDiscount($lineItemId, $isPaymentFrequencyDiscountValueDiff);
        $customerDiscount = $model->item->loadCustomerDiscount($lineItemId, $isCustomerDiscountValueDiff);
        $multiEnrolmentDiscount = $model->item->loadMultiEnrolmentDiscount($lineItemId, $isMultiEnrolmentDiscountValueDiff);
        $data = $this->renderAjax('/invoice/_form-apply-discount', [
            'lineItemIds' => $lineItemIds,
            'model' => $model,
            'customerDiscount' => $customerDiscount,
            'paymentFrequencyDiscount' => $paymentFrequencyDiscount,
            'lineItemDiscount' => $lineItemDiscount,
            'multiEnrolmentDiscount' => $multiEnrolmentDiscount
        ]);
        $post = Yii::$app->request->post();
        if ($post) {
            foreach ($lineItemIds as $lineItemId) {
                $model = $this->findModel($lineItemId);
                $lineItemDiscount = $model->item->loadLineItemDiscount($lineItemId);
                $lineItemDiscount->load($post);
                $customerDiscount = $model->item->loadCustomerDiscount($lineItemId);
                $customerDiscount->load($post);
                $lineItemDiscount->save();
                $customerDiscount->save();
                if ($model->isLessonItem()) {
                    $paymentFrequencyDiscount = $model->item->loadPaymentFrequencyDiscount($lineItemId);
                    $paymentFrequencyDiscount->load($post);
                    $multiEnrolmentDiscount = $model->item->loadMultiEnrolmentDiscount($lineItemId);
                    $multiEnrolmentDiscount->load($post);
                    $paymentFrequencyDiscount->save();
                    $multiEnrolmentDiscount->save();
                }
            }
            return [
                'status' => true
            ];
        } else {
            return [
                'status' => true,
                'data' => $data
            ];
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
        if (!$invoiceLineItem->isLessonItem()) {
            $taxCode           = $invoiceLineItem->computeTaxCode($data['taxStatus']);
            $invoiceLineItem->tax_status = $taxCode->taxStatus->name;
            $invoiceLineItem->tax_type   = $taxCode->taxType->name;
        }
        $discount = 0.0;
        $lineItemPrice = $invoiceLineItem->grossPrice;
        if (!empty($data['multiEnrolmentDiscount'])) {
            $discount += $lineItemPrice < 0 ? - ($data['multiEnrolmentDiscount']) : $data['multiEnrolmentDiscount'];
            $lineItemPrice = $invoiceLineItem->grossPrice - $discount;
        }
        if (!empty($data['lineItemDiscount'])) {
            if ($data['lineItemDiscountType']) {
                $discount += $lineItemPrice < 0 ? - ($data['lineItemDiscount']) : $data['lineItemDiscount'];
            } else {
                $discount += $lineItemPrice * $data['lineItemDiscount'] / 100;
            }
            $lineItemPrice = $invoiceLineItem->grossPrice - $discount;
        }
        if (!empty($data['customerDiscount'])) {
            $discount += $lineItemPrice * $data['customerDiscount'] / 100;
            $lineItemPrice = $invoiceLineItem->grossPrice - $discount;
        }
        if (!empty($data['paymentFrequencyDiscount'])) {
            $discount += $lineItemPrice * $data['paymentFrequencyDiscount'] / 100;
        }
        
        $netPrice = $invoiceLineItem->grossPrice - $discount;
        $invoiceLineItem->tax_rate   = $netPrice * $invoiceLineItem->taxType->taxCode->rate / 100;
        $itemTotal = $netPrice + $invoiceLineItem->tax_rate;
        return [
            'grossPrice' => Yii::$app->formatter->asDecimal($invoiceLineItem->grossPrice, 4),
            'itemTotal' => Yii::$app->formatter->asDecimal($itemTotal, 4),
            'netPrice' => Yii::$app->formatter->asDecimal($netPrice, 4),
            'taxRate' => Yii::$app->formatter->asDecimal($invoiceLineItem->tax_rate, 4),
            'taxPercentage' => $invoiceLineItem->taxType->taxCode->rate
        ];
    }
}
