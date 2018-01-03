<?php

namespace backend\controllers;

use common\models\Payment;
use Yii;
use common\models\Location;
use common\models\User;
use common\models\Item;
use common\models\Invoice;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\Student;
use common\models\InvoiceLineItem;
use common\models\ItemType;
use common\models\PaymentMethod;
use yii\web\Response;
use yii\widgets\ActiveForm;
use Carbon\Carbon;

/**
 * UserController implements the CRUD actions for User model.
 */
class CustomerController extends UserController
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
                'only' => ['merge'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
        ];
    }

    public function actions()
    {
		
    }

    public function actionAddOpeningBalance($id)
    {
        $model = $this->findModel($id);
        $locationId = Location::findOne(['slug' => Yii::$app->location])->id;
        $paymentModel = new Payment(['scenario' => Payment::SCENARIO_OPENING_BALANCE]);
        if ($paymentModel->load(Yii::$app->request->post())) {
            $invoice = new Invoice();
            $invoice->user_id = $model->id;
            $invoice->location_id = $locationId;
            $invoice->type = Invoice::TYPE_INVOICE;
            $invoice->save();

            $invoiceLineItem = new InvoiceLineItem(['scenario' => InvoiceLineItem::SCENARIO_OPENING_BALANCE]);
            $invoiceLineItem->invoice_id = $invoice->id;
            $item = Item::findOne(['code' => Item::OPENING_BALANCE_ITEM]);
            $invoiceLineItem->item_id = $item->id;
            $invoiceLineItem->item_type_id = ItemType::TYPE_OPENING_BALANCE;
            $invoiceLineItem->description = $item->description;
            $invoiceLineItem->unit = 1;
            $invoiceLineItem->amount = 0;
            $invoiceLineItem->code = $invoiceLineItem->getItemCode();
            $invoiceLineItem->cost = 0;
            if ($paymentModel->amount > 0) {
                $invoiceLineItem->amount = $paymentModel->amount;
                $invoice->subTotal = $invoiceLineItem->amount;
            } else {
                $invoice->subTotal = 0.00;
            }
            $invoiceLineItem->save();
            $invoice->tax = $invoiceLineItem->tax_rate;
            $invoice->total = $invoice->subTotal + $invoice->tax;
            if(!empty($invoice->location->conversionDate)) {
                $date = Carbon::parse($invoice->location->conversionDate);
            	$invoice->date = $date->subDay(1);
            }
            $invoice->save();

            if ($paymentModel->amount < 0) {
                $paymentModel->date = (new \DateTime($paymentModel->date))->format('Y-m-d H:i:s');
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

    protected function findModel($id)
    {
        $session = Yii::$app->session;
        $locationId = Location::findOne(['slug' => Yii::$app->location])->id;
        $model = User::find()->location($locationId)
                ->where(['user.id' => $id])
                ->notDeleted()
                ->one();
        if ($model !== null) {
            return $model;
        }else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionMerge($id)
    {
        $model = $this->findModel($id);
        $model->setScenario(User::SCENARIO_MERGE);
        $data       = $this->renderAjax('/user/customer/_merge', [
            'model' => $model,
        ]);
        $post = Yii::$app->request->post();
        if ($model->load($post)) {
            if ($model->validate()) {
                foreach ($model->customerIds as $customerId) {
                    $customer = User::findOne($customerId);
                    foreach ($customer->students as $student) {
                        $student->setScenario(Student::SCENARIO_CUSTOMER_MERGE);
                        $student->customer_id = $id;
                        $student->save();
                    }
                    foreach ($customer->notes as $note) {
                        $note->instanceId = $id;
                        $note->save();
                    }
                    foreach ($customer->logs as $log) {
                        $log->userId = $id;
                        $log->save();
                    }
                    $customer->softDelete();
                }
                return [
                    'status' => true,
                    'message' => 'Customer successfully merged!'
                ];
            } else {
                $errors = ActiveForm::validate($model);
                return [
                    'status' => false,
                    'errors' => current($errors)
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
