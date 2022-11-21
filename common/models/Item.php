<?php

namespace common\models;

use Yii;
use backend\models\discount\LineItemDiscount;
use backend\models\discount\CustomerLineItemDiscount;
use backend\models\discount\PaymentFrequencyLineItemDiscount;
use backend\models\discount\EnrolmentLineItemDiscount;
use yii2tech\ar\softdelete\SoftDeleteBehavior;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "item".
 *
 * @property string $id
 * @property string $itemCategoryId
 * @property string $locationId
 * @property string $code
 * @property string $description
 * @property double $price
 * @property integer $royaltyFree
 * @property string $taxStatusId
 * @property integer $status
 * @property integer $isDeleted
 */
class Item extends \yii\db\ActiveRecord
{
    const LESSON_ITEM = 'LESSON';
    const OPENING_BALANCE_ITEM = 'OPENING BALANCE';
    const LESSON_CREDIT = 'LESSON CREDIT';
    const PAYMENT_CREDIT = 'PAYMENT CREDIT';
    const DEFAULT_ITEMS = 0;

    const STATUS_ENABLED  = 1;
    const STATUS_DISABLED = 0;

    const ROYALTY_FREE  = 1;
    const NOT_ROYALTY_FREE = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'item';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['itemCategoryId', 'locationId', 'code', 'royaltyFree', 'taxStatusId', 'status', 
                'description','price'], 'required'],
            [['itemCategoryId', 'locationId', 'royaltyFree', 'taxStatusId', 'status'], 'integer'],
            [['price'], 'number'],
            [['description', 'code'], 'trim'],
            ['isDeleted', 'safe'],
            [['code'], 'string', 'max' => 150],
            [['description'], 'string', 'max' => 500],
        ];
    }

    public function behaviors()
    {
        return [
            'softDeleteBehavior' => [
                'class' => SoftDeleteBehavior::className(),
                'softDeleteAttributeValues' => [
                    'isDeleted' => true,
                ],
                'replaceRegularDelete' => true
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'itemCategoryId' => 'Item Category',
            'locationId' => 'Location ID',
            'code' => 'Code',
            'description' => 'Description',
            'price' => 'Price',
            'royaltyFree' => 'Royalty Free',
            'taxStatusId' => 'Tax Status',
            'status' => 'status',
            'showAllItems' => 'Show All'
        ];
    }

    public static function itemStatuses()
    {
        return [
            self::STATUS_ENABLED => Yii::t('common', 'Enable'),
            self::STATUS_DISABLED => Yii::t('common', 'Disable'),
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\ItemQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\ItemQuery(get_called_class());
    }

    public function getItemCategory()
    {
        return $this->hasOne(ItemCategory::className(), ['id' => 'itemCategoryId']);
    }

    public function getTaxStatus()
    {
        return $this->hasOne(TaxStatus::className(), ['id' => 'taxStatusId']);
    }

    public function getStatusType()
    {
        switch ($this->status) {
            case self::STATUS_ENABLED:
                $status = 'Enable';
            break;
            case self::STATUS_DISABLED:
                $status = 'Disable';
            break;
        }

        return $status;
    }

    public function getRoyaltyFreeStatus()
    {
        switch ($this->royaltyFree) {
            case self::ROYALTY_FREE:
                $status = 'Yes';
            break;
            case self::NOT_ROYALTY_FREE:
                $status = 'No';
            break;
        }

        return $status;
    }

    public function isOpeningBalance()
    {
        return $this->code === self::OPENING_BALANCE_ITEM;
    }

    public function isLesson()
    {
        return $this->code === self::LESSON_ITEM;
    }

    public function canUpdate()
    {
        return !$this->isLesson() && !$this->isOpeningBalance();
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->isDeleted = false;
        }

        return parent::beforeSave($insert);
    }

    public function getItemTotal($locationId, $date)
    {
        $amount = 0;
        $items = InvoiceLineItem::find()
                ->notDeleted()
                ->joinWith(['invoice' => function ($query) use ($locationId) {
                    $query->notDeleted()
                        ->location($locationId);
                }])
                ->andWhere([
                    'invoice_line_item.item_id' => $this->id,
                    'DATE(invoice.date)' => (new \DateTime($date))->format('Y-m-d')
                ])
                ->all();
        foreach ($items as $item) {
            $amount += $item->itemTotal;
        }

        return $amount;
    }
    
    public function loadCustomerDiscount($id, $value = null)
    {
        if (!$this->isOpeningBalance()) {
            $lineItem = InvoiceLineItem::findOne($id);
            $customerDiscount = new CustomerLineItemDiscount();
            if ($lineItem->hasCustomerDiscount()) {
                $customerDiscount = $customerDiscount->setModel($lineItem->customerDiscount, $value);
            }
            $customerDiscount->invoiceLineItemId = $id;
            return $customerDiscount;
        }
        return null;
    }
    
    public function loadPaymentFrequencyDiscount($id, $value = null)
    {
        if ($this->isLesson()) {
            $lineItem = InvoiceLineItem::findOne($id);
            $paymentFrequencyDiscount = new PaymentFrequencyLineItemDiscount();
            if ($lineItem->hasEnrolmentPaymentFrequencyDiscount()) {
                $paymentFrequencyDiscount = $paymentFrequencyDiscount->setModel(
                        $lineItem->enrolmentPaymentFrequencyDiscount,
                    $value
                );
            }
            $paymentFrequencyDiscount->invoiceLineItemId = $id;
            return $paymentFrequencyDiscount;
        }
        return null;
    }
    
    public function loadLineItemDiscount($id, $value = null)
    {
        if (!$this->isOpeningBalance()) {
            $lineItem = InvoiceLineItem::findOne($id);
            $lineItemDiscount = new LineItemDiscount();
            if ($lineItem->hasLineItemDiscount()) {
                $lineItemDiscount = $lineItemDiscount->setModel($lineItem->lineItemDiscount, $value);
            }
            $lineItemDiscount->invoiceLineItemId = $id;
            return $lineItemDiscount;
        }
        return null;
    }
    
    public function loadMultiEnrolmentDiscount($id, $value = null)
    {
        if ($this->isLesson()) {
            $lineItem = InvoiceLineItem::findOne($id);
            $multiEnrolmentDiscount = new EnrolmentLineItemDiscount();
            if ($lineItem->hasMultiEnrolmentDiscount()) {
                $multiEnrolmentDiscount = $multiEnrolmentDiscount->setModel(
                        $lineItem->multiEnrolmentDiscount,
                    $value
                );
            }
            $multiEnrolmentDiscount->invoiceLineItemId = $id;
            return $multiEnrolmentDiscount;
        }
        return null;
    }
    
    public function addToInvoice($invoice)
    {
        $invoiceLineItemModel = null;
        foreach ($invoice->lineItems as $lineItem) {
            if ($lineItem->item_id === $this->id) {
                $lineItem->unit += 1;
                $lineItem->setScenario(InvoiceLineItem::SCENARIO_MISC);
                $lineItem->save();
                $invoiceLineItemModel = $lineItem;
                break;
            }
        }
        if (!$invoiceLineItemModel) {
            $invoiceLineItemModel = new InvoiceLineItem(['scenario' => InvoiceLineItem::SCENARIO_MISC]);
            $invoiceLineItemModel->invoice_id = $invoice->id;
            $invoiceLineItemModel->item_id = $this->id;
            $invoiceLineItemModel->unit = true;
            $invoiceLineItemModel->description = $this->description;
            $invoiceLineItemModel->amount = $this->price;
            $invoiceLineItemModel->item_type_id = ItemType::TYPE_MISC;
            $invoiceLineItemModel->cost        = 0.0;
            if (!$invoiceLineItemModel->save()) {
                Yii::error('Line item create error: '.VarDumper::dumpAsString($invoiceLineItemModel->getErrors()));
            }
        }
        return $invoiceLineItemModel;
    }
}
