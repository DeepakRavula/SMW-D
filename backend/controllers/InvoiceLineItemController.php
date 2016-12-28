<?php

namespace backend\controllers;

use Yii;
use common\models\Payment;
use common\models\PaymentMethod;
use common\models\InvoiceLineItem;
use yii\web\Response;
use yii\bootstrap\ActiveForm;
use common\models\Invoice;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

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
        ];
    }

    public function actionEdit($id)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if (Yii::$app->request->post('hasEditable')) {
            $lineItemIndex = Yii::$app->request->post('editableIndex');
            $model = InvoiceLineItem::findOne(['id' => $id]);
            $result = [
                'output' => '',
                'message' => '',
            ];
            $post = Yii::$app->request->post();
            if (!empty($post['InvoiceLineItem'][$lineItemIndex]['description'])) {
                $model->description = $post['InvoiceLineItem'][$lineItemIndex]['description'];
                $output = $model->description;
                $model->save();
            }
            if ($post['InvoiceLineItem'][$lineItemIndex]['discount']) {
                $model->discount = $post['InvoiceLineItem'][$lineItemIndex]['discount'];
                $model->discountType = $post['InvoiceLineItem'][$lineItemIndex]['discountType'];
                $output = $model->discount;
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
            $model->invoice->save();
            return $result;
        }
    }

    public function actionEditOpeningBalance($model, $newAmount)
    {
        $model->setScenario(InvoiceLineItem::SCENARIO_OPENING_BALANCE);
        $model->amount = $newAmount;
        $model->save();
        $payments = $model->invoice->payment;
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
        $model->save();
        $model->invoice->save();
        $result = [
            'output' => $newAmount,
            'message' => '',
        ];

        return $result;
    }

    public function actionApplyDiscount($id)
    {
        $response = \Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        $invoiceModel = Invoice::findOne($id);
        $invoiceModel->setScenario(Invoice::SCENARIO_DISCOUNT);
        if ($invoiceModel->load(Yii::$app->request->post())) {
            if ($invoiceModel->validate()) {
                $invoiceLineItems = $invoiceModel->lineItems;
                foreach ($invoiceLineItems as $invoiceLineItem) {
                    $invoiceLineItem->discount = $invoiceModel->discount;
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
        $invoiceModel = $model->invoice;
        $model->delete();
        $invoiceModel->save();
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
