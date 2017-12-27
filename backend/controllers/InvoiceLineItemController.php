<?php

namespace backend\controllers;

use Yii;
use common\models\Payment;
use common\models\PaymentMethod;
use backend\models\discount\LineItemMultiDiscount;
use common\models\InvoiceLineItem;
use yii\web\Response;
use yii\filters\ContentNegotiator;
use yii\bootstrap\ActiveForm;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use common\models\User;
use common\models\InvoiceLog;
use backend\models\LineItemMultiTax;
use yii\helpers\Json;
use common\models\Location;
use common\models\TaxCode;
/**
 * InvoiceController implements the CRUD actions for Invoice model.
 */
class InvoiceLineItemController extends \common\components\backend\BackendController
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
                'only' => ['edit', 'apply-discount', 'update', 'compute-net-price', 
                    'delete', 'edit-tax'],
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
   	public function actionFetchTaxPercentage($taxStatusId)
	{
		$today         = (new \DateTime())->format('Y-m-d H:i:s');
        $locationId    = \Yii::$app->session->get('location_id');
        $locationModel = Location::findOne(['id' => $locationId]);
        $taxCode = TaxCode::find()
            ->joinWith(['taxStatus' => function ($query) use ($taxStatusId) {
                $query->where(['tax_status.name' => $taxStatusId]);
            }])
            ->where(['<=', 'start_date', $today])
            ->andWhere(['province_id' => $locationModel->province_id])
            ->orderBy('start_date DESC')
            ->one();
		return $taxCode->rate;
	} 
    public function actionUpdate($id) 
    {
        $lineItem = $this->findModel($id);
        if ($lineItem->invoice->isReversedInvoice()) {
            $lineItem->setScenario(InvoiceLineItem::SCENARIO_NEGATIVE_VALUE_EDIT);
        }
        $data = $this->renderAjax('/invoice/line-item/_form', [
            'model' => $lineItem
        ]);
        $post = Yii::$app->request->post();
        if ($lineItem->load($post)) {
            if($lineItem->save()) {
                $response = [
                    'status' => true,
                    'message' => 'Item successfully updated!',
		];	
            } else {
                $response = [
                    'status' => false,
                    'errors' => ActiveForm::validate($lineItem),
                ];	
            }
            return $response;
        } else {

            return [
                'status' => true,
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
        $lineItemId = end($lineItemIds);
        $model = $this->findModel($lineItemId);
        $lineItemDiscount = LineItemMultiDiscount::loadLineItemDiscount($lineItemIds);
        $paymentFrequencyDiscount = LineItemMultiDiscount::loadPaymentFrequencyDiscount($lineItemIds);
        $customerDiscount = LineItemMultiDiscount::loadCustomerDiscount($lineItemIds);
        $multiEnrolmentDiscount = LineItemMultiDiscount::loadEnrolmentDiscount($lineItemIds);
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
                $lineItemDiscount = LineItemMultiDiscount::loadLineItemDiscount([$lineItemId]);
                $customerDiscount = LineItemMultiDiscount::loadCustomerDiscount([$lineItemId]);
                $lineItemDiscount->load($post);
                $customerDiscount->load($post);
                $lineItemDiscount->save();
                $customerDiscount->save();
                if ($model->isLessonItem()) {
                    $paymentFrequencyDiscount = LineItemMultiDiscount::loadPaymentFrequencyDiscount([$lineItemId]);
                    $multiEnrolmentDiscount = LineItemMultiDiscount::loadEnrolmentDiscount([$lineItemId]);
                    $paymentFrequencyDiscount->load($post);
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
        $invoiceModel = $model->invoice;
        if($model->delete()) {
            $invoiceModel->save();
        }
        return [
            'status' => true,
            'message' => 'Line Item has been deleted successfully'
        ];

        
    }

    protected function findModel($id)
    {
        $session = Yii::$app->session;
        $locationId = \Yii::$app->session->get('location_id');
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
    
    public function actionEditTax()
    {
        $lineItemIds = Yii::$app->request->get('InvoiceLineItem')['ids'];
        $multiLineItemTax = new LineItemMultiTax(); 
        $lineItem = $multiLineItemTax->setModel($lineItemIds);
        $lineItem->setScenario(InvoiceLineItem::SCENARIO_EDIT);
        $data = $this->renderAjax('/invoice/line-item/_form-tax', [
            'lineItemIds' => $lineItemIds,
            'model' => $lineItem
        ]);
        $post = Yii::$app->request->post();
        if ($post) {
            foreach ($lineItemIds as $lineItemId) {
                $lineItem = InvoiceLineItem::findOne($lineItemId);
                $lineItem->load($post);
                if (!$lineItem->save()) {
                    Yii::error('Line item discount error: '.VarDumper::dumpAsString($lineItem->getErrors()));
                }
            }
            $lineItem->invoice->isTaxAdjusted = false;
            $lineItem->invoice->save();
            return [
                'status' => true,
                'message' => 'Tax successfully updated!'
            ];
        } else {
            return [
                'status' => true,
                'data' => $data
            ];
        }
    }
}
