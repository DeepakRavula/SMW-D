<?php

namespace backend\controllers;

use Yii;
use backend\models\discount\LineItemMultiDiscount;
use common\models\InvoiceLineItem;
use yii\web\Response;
use yii\filters\ContentNegotiator;
use yii\bootstrap\ActiveForm;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use backend\models\LineItemMultiTax;
use yii\helpers\Json;
use common\models\Location;
use common\models\TaxCode;
use common\components\controllers\BaseController;
use yii\filters\AccessControl;

/**
 * InvoiceController implements the CRUD actions for Invoice model.
 */
class InvoiceLineItemController extends BaseController
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
			'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['compute-tax', 'fetch-tax-percentage', 'update', 'delete', 'edit-tax', 'apply-discount'],
                        'roles' => ['manageInvoices', 'managePfi'],
                    ],
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
        $locationId    = Location::findOne(['slug' => \Yii::$app->location])->id;
        $locationModel = Location::findOne(['id' => $locationId]);
        $taxCode = TaxCode::find()
            ->joinWith(['taxStatus' => function ($query) use ($taxStatusId) {
                $query->andWhere(['tax_status.name' => $taxStatusId]);
            }])
            ->andWhere(['<=', 'start_date', $today])
            ->andWhere(['province_id' => $locationModel->province_id])
            ->orderBy('start_date DESC')
            ->one();
        return $taxCode->rate;
    }

    public function actionUpdate($id)
    {
        $lineItem = $this->findModel($id);
        $confirmationMessage = null;
        if ($lineItem->isLessonItem() && $lineItem->invoice->isInvoice()) {
            $confirmationMessage = 'Deleting line item will unschedule the lesson. Would you like to proceed?';
        }
        if (!$lineItem->invoice->isPosted && !$lineItem->invoice->isPaymentCreditInvoice()) {
            if ($lineItem->invoice->isReversedInvoice()) {
                $lineItem->setScenario(InvoiceLineItem::SCENARIO_NEGATIVE_VALUE_EDIT);
            }
            if ($lineItem->isLessonCredit() || $lineItem->isOpeningBalance()) {
                $lineItem->setScenario(InvoiceLineItem::SCENARIO_OPENING_BALANCE);
            }
            if ($lineItem->isMisc()) {
                $lineItem->setScenario(InvoiceLineItem::SCENARIO_MISC);
            }
            $data = $this->renderAjax('/invoice/line-item/_form', [
                'model' => $lineItem
            ]);
            $post = Yii::$app->request->post();
            if ($lineItem->load($post)) {
                if ($lineItem->save()) {
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
            } else {
                $response = [
                    'status' => true,
                    'data' => $data,
                    'deleteConfirmation' => $confirmationMessage
                ];
            }
        } else {
            $response = [
                'status' => false,
                'message' => 'Item cannot be updated! ',
            ];
        }
        return $response;
    }

    public function actionApplyDiscount()
    {
        $lineItemIds = Yii::$app->request->get('InvoiceLineItem')['ids'];
        $lineItemId = end($lineItemIds);
        $model = $this->findModel($lineItemId);
        if (!$model->invoice->isPosted) {
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
                $response = [
                    'status' => true
                ];
            } else {
                return [
                    'status' => true,
                    'data' => $data
                ];
            }
        } else {
            $response = [
                'status' => false,
                'message' => 'Discount cannot be applied if invoice posted'
            ];
        }
        return $response;
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if (!$model->invoice->isPosted) {
            $invoiceModel = $model->invoice;
            if ($model->delete()) {
               // $invoiceModel->save();
            }
            $response = [
                'status' => true,
                'message' => 'Line Item has been deleted successfully'
            ];
        } else {
            $response = [
                'status' => false,
                'message' => 'Line Item cannot be deleted if invoice posted'
            ];
        }
        return $response;
    }

    protected function findModel($id)
    {
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $model = InvoiceLineItem::find()
                ->joinWith(['invoice' => function ($query) use ($locationId) {
                    $query->andWhere(['location_id' => $locationId]);
                }])
                ->andWhere([
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
        $model = $this->findModel(end($lineItemIds));
        if (!$model->invoice->isPosted) {
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
                $response = [
                    'status' => true,
                    'message' => 'Tax successfully updated!'
                ];
            } else {
                $response = [
                    'status' => true,
                    'data' => $data
                ];
            }
        } else {
            $response = [
                'status' => false,
                'message' => 'Tax cannot be updated if invoice posted!'
            ];
        }
        return $response;
    }
}
