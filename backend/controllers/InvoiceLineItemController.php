<?php

namespace backend\controllers;

use Yii;
use common\models\Location;
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
                'only' => ['edit', 'apply-discount'],
                'formatParam' => '_format',
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
        ];
    }

    public function actionEdit($id)
    {
        if (Yii::$app->request->post('hasEditable')) {
            $lineItemIndex = Yii::$app->request->post('editableIndex');
            $model = InvoiceLineItem::findOne(['id' => $id]);
			$model->on(InvoiceLineItem::EVENT_EDIT, [new InvoiceLog(), 'edit'],['oldAttribute' => $model->getOldAttributes()]);
			$user = User::findOne(['id' => Yii::$app->user->id]);
			$model->userName = $user->publicIdentity;
            $result = [
                'output' => '',
                'message' => '',
            ];
            $post = Yii::$app->request->post();
            if (!empty($post['InvoiceLineItem'][$lineItemIndex]['description'])) {
                $model->description = $post['InvoiceLineItem'][$lineItemIndex]['description'];
                if($model->save()) {
					$model->trigger(InvoiceLineItem::EVENT_EDIT);
				}
            }
            if (isset($post['InvoiceLineItem'][$lineItemIndex]['isRoyalty'])) {
                $model->isRoyalty = $post['InvoiceLineItem'][$lineItemIndex]['isRoyalty'];
                $model->save();
            }
            if (!empty($post['InvoiceLineItem'][$lineItemIndex]['tax_status'])) {
                $tax_status     = $post['InvoiceLineItem'][$lineItemIndex]['tax_status'];
                $today         = (new \DateTime())->format('Y-m-d H:i:s');
                $locationId    = Yii::$app->session->get('location_id');
                $locationModel = Location::findOne(['id' => $locationId]);
                $taxCode = TaxCode::find()
                    ->joinWith(['taxStatus' => function ($query) use ($tax_status) {
                        $query->where(['tax_status.id' => $tax_status]);
                    }])
                    ->where(['<=', 'start_date', $today])
                    ->andWhere(['province_id' => $locationModel->province_id])
                    ->orderBy('start_date DESC')
                    ->one();
                $model->tax_status = $taxCode->taxStatus->name;
                $model->tax_type   = $taxCode->taxType->name;
                $model->save();
            }
            if (isset($post['InvoiceLineItem'][$lineItemIndex]['discount']) &&
                    isset($post['InvoiceLineItem'][$lineItemIndex]['discountType'])) {
                $model->discount = $post['InvoiceLineItem'][$lineItemIndex]['discount'];
                $model->discountType = $post['InvoiceLineItem'][$lineItemIndex]['discountType'];
                $model->save();
				}
            if (!empty($post['InvoiceLineItem'][$lineItemIndex]['amount'])) {
                $newAmount = $post['InvoiceLineItem'][$lineItemIndex]['amount'];
                if ($model->isOpeningBalance()) {
                    $result = Yii::$app->runAction('invoice-line-item/edit-opening-balance',
                        ['model' => $model, 'newAmount' => $newAmount]);
                }
                if ($model->isOtherLineItems()) {
                    $result = Yii::$app->runAction('invoice-line-item/edit-other-items',
                        ['model' => $model, 'newAmount' => $newAmount]);
                }
            }
            return $result;
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
}
