<?php

namespace common\models;

use Yii;
use common\models\ItemType;
use common\models\TaxStatus;
use common\models\query\InvoiceLineItemQuery;
use common\models\discount\InvoiceLineItemDiscount;
use yii2tech\ar\softdelete\SoftDeleteBehavior;
use asinfotrack\yii2\audittrail\behaviors\AuditTrailBehavior;
use backend\models\discount\LineItemDiscount;

/**
 * This is the model class for table "invoice_line_item".
 *
 * @property int $id
 * @property int $invoice_id
 * @property int $item_id
 * @property float $unit
 * @property float $discount
 * @property float $discountType
 * @property string $amount
 */
class InvoiceLineItem extends \yii\db\ActiveRecord
{
    const SCENARIO_OPENING_BALANCE = 'allow-negative-line-item-amount';
    const SCENARIO_MISC = 'misc-item';
    const SCENARIO_EDIT = 'edit';
    const SCENARIO_NEGATIVE_VALUE_EDIT = 'negative-value-edit';
    const DISCOUNT_FLAT            = 0;
    const DISCOUNT_PERCENTAGE      = 1;


    const EVENT_EDIT = 'edit';
    const EVENT_DELETE = 'deleteLineItem';
    const EVENT_CREATE= 'newLineItem';
    
    const STATUS_DEFAULT = 'Default';
    const STATUS_NO_TAX = 'No Tax';
    const STATUS_GST_ONLY = 'GST Only';
    const CONSOLE_USER_ID  = 727;

    public $userName;
    public $price;
    public $tax;
    public $lessonCanBeUnscheduled = true;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'invoice_line_item';
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
            ],
            'audittrail' => [
                'class' => AuditTrailBehavior::className(), 
                'consoleUserId' => self::CONSOLE_USER_ID, 
                'attributeOutput' => [
                    'last_checked' => 'datetime',
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['tax_status', 'required', 'on' => self::SCENARIO_EDIT],
            [['unit', 'item_id', 'description'], 'required', 'when' => function ($model) {
                return (int) $model->item_type_id === ItemType::TYPE_MISC;
            }],
            [['amount'], 'number', 'when' => function ($model) {
                return (int) $model->item_type_id === ItemType::TYPE_MISC;
            }],
            [['description'], 'trim'],
            ['amount', 'compare', 'operator' => '>', 'compareValue' => 0, 'except' => [self::SCENARIO_OPENING_BALANCE,
                self::SCENARIO_NEGATIVE_VALUE_EDIT, self::SCENARIO_MISC]],
            [['unit'], 'number', 'when' => function ($model) {
                return (int) $model->item_type_id !== ItemType::TYPE_MISC;
            },
            ],
            ['amount', 'default', 'value' => 0.0000],
            [['netTotal'], 'number'],
            [['royaltyFree', 'invoice_id', 'item_id', 'item_type_id', 'tax_code', 'tax_status', 'tax_type', 
                'tax_rate', 'userName', 'cost', 'code', 'rate', 'isDeleted', 'lessonCanBeUnscheduled'], 'safe'],
        ];
    }

    public static function find()
    {
        return new InvoiceLineItemQuery(get_called_class());
    }

    public function getLesson()
    {
        $query = $this->hasOne(Lesson::className(), ['id' => 'lessonId']);
        if ($this->isPaymentCycleLesson()) {
            return $query->via('paymentCycleLesson');
        } else {
            return $query->via('lineItemLesson');
        }
    }
    
    public function getPaymentCycleLesson()
    {
        if ($this->isPaymentCycleLesson()) {
            return $this->hasOne(PaymentCycleLesson::className(), ['id' => 'paymentCycleLessonId'])
                    ->via('lineItemPaymentCycleLesson');
        } else {
            return $this->hasOne(PaymentCycleLesson::className(), ['lessonId' => 'lessonId'])
                    ->via('lineItemLesson');
        }
    }
    
    public function getPaymentCycle()
    {
        return $this->hasOne(PaymentCycle::className(), ['id' => 'paymentCycleId'])
                ->via('paymentCycleLesson');
    }

    public function getItemType()
    {
        return $this->hasOne(ItemType::className(), ['id' => 'item_type_id']);
    }

    public function getItem()
    {
        return $this->hasOne(Item::className(), ['id' => 'item_id']);
    }

    public function getItemCategory()
    {
        return $this->hasOne(ItemCategory::className(), ['id' => 'itemCategoryId'])
            ->via('item');
    }
    
    public function getTaxStatus()
    {
        $code = null;
        switch ($this->tax_status) {
            case self::STATUS_DEFAULT:
                $code = TaxStatus::STATUS_DEFAULT;
            break;
            case self::STATUS_NO_TAX:
                $code = TaxStatus::STATUS_NO_TAX;
            break;
            case self::STATUS_GST_ONLY:
                $code = TaxStatus::STATUS_GST_ONLY;
            break;
        }

        return $code;
    }

    public function getItemCode()
    {
        return $this->itemType->getItemCode();
    }

    public function getLineItemLesson()
    {
        return $this->hasOne(InvoiceItemLesson::className(), ['invoiceLineItemId' => 'id']);
    }

    public function getDiscounts()
    {
        return $this->hasMany(InvoiceLineItemDiscount::className(), ['invoiceLineItemId' => 'id']);
    }

    public function getEnrolment()
    {
        if ($this->isGroupLesson()) {
            return $this->hasOne(Enrolment::className(), ['id' => 'enrolmentId'])
                ->via('lineItemEnrolment');
        } else {
            if ($this->isPaymentCycleLesson()) {
                return $this->hasOne(Enrolment::className(), ['id' => 'enrolmentId'])
                    ->via('paymentCycle');
            } else {
                return $this->hasOne(Enrolment::className(), ['courseId' => 'courseId'])
                    ->via('lesson');
            }
        }
    }
    public function getItemDiscounts()
    {
        return $this->hasMany(InvoiceLineItemDiscount::className(), ['invoiceLineItemId' => 'id']);
    }

    public function getLineItemDiscount()
    {
        return $this->hasOne(InvoiceLineItemDiscount::className(), ['invoiceLineItemId' => 'id'])
            ->onCondition(['invoice_line_item_discount.type' => InvoiceLineItemDiscount::TYPE_LINE_ITEM]);
    }

    public function getCustomerDiscount()
    {
        return $this->hasOne(InvoiceLineItemDiscount::className(), ['invoiceLineItemId' => 'id'])
            ->onCondition(['invoice_line_item_discount.type' => InvoiceLineItemDiscount::TYPE_CUSTOMER]);
    }

    public function getEnrolmentPaymentFrequencyDiscount()
    {
        return $this->hasOne(InvoiceLineItemDiscount::className(), ['invoiceLineItemId' => 'id'])
            ->onCondition(['invoice_line_item_discount.type' => InvoiceLineItemDiscount::TYPE_ENROLMENT_PAYMENT_FREQUENCY]);
    }

    public function getGroupDiscount()
    {
        return $this->hasOne(InvoiceLineItemDiscount::className(), ['invoiceLineItemId' => 'id'])
            ->onCondition(['invoice_line_item_discount.type' => InvoiceLineItemDiscount::TYPE_GROUP]);
    }

    public function getMultiEnrolmentDiscount()
    {
        return $this->hasOne(InvoiceLineItemDiscount::className(), ['invoiceLineItemId' => 'id'])
            ->onCondition(['invoice_line_item_discount.type' => InvoiceLineItemDiscount::TYPE_MULTIPLE_ENROLMENT]);
    }

    public function getLineItemEnrolment()
    {
        return $this->hasOne(InvoiceItemEnrolment::className(), ['invoiceLineItemId' => 'id']);
    }

    public function getLineItemPaymentCycleLesson()
    {
        return $this->hasOne(InvoiceItemPaymentCycleLesson::className(), ['invoiceLineItemId' => 'id']);
    }

    public function getLineItemPaymentCycleLessons()
    {
        return $this->hasMany(InvoiceItemPaymentCycleLesson::className(), ['invoiceLineItemId' => 'id']);
    }

    public function getInvoice()
    {
        return $this->hasOne(Invoice::className(), ['id' => 'invoice_id']);
    }
    public function getProformaInvoiceItem()
    {
        return $this->hasOne(ProformaItemInvoice::className(), ['invoice_id' => 'id']);
    }
    public function getProFormaLesson()
    {
        if (!$this->isExtraLesson()) {
            return $this->hasOne(Lesson::className(), ['id' => 'lessonId'])
                        ->via('paymentCycleLesson');
        } else {
            return $this->lesson;
        }
    }

    public function getOriginalInvoice()
    {
        return $this->hasOne(Invoice::className(), ['id' => 'invoice_id'])
                ->andWhere(['invoice.type' => Invoice::TYPE_INVOICE]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'item_type_id' => 'Code',
            'invoice_id' => 'Invoice ID',
            'lesson_id' => 'Lesson ID',
            'unit' => 'Quantity',
            'amount' => 'Amount',
            'discount' => 'Discount',
            'discountType' => 'Discount Type',
            'description' => 'Description',
            'tax_rate' => 'Tax',
            'tax_status' => 'Tax Status',
            'royaltyFree' => 'Royalty Free',
            'tax' => 'Tax (%)',
            'itemCategoryId' => 'Item Category',
            'item_id' => 'Item',
            'groupByMethod' => 'Summaries Only',
        ];
    }

    public function isLessonItem()
    {
        $lessonItemCategory = ItemCategory::findOne(['name' => 'Lesson']);
        return (int) $this->item->itemCategoryId === (int) $lessonItemCategory->id;
    }

    public function afterSoftDelete()
    {
        $invoiceModel = $this->invoice;
        if (!$invoiceModel->hasLineItem() && !$invoiceModel->isInvoice()) {
            $invoiceModel->setScenario(Invoice::SCENARIO_DELETE);
            if ($invoiceModel->validate()) {
                $invoiceModel->delete();
            }
        }
        if ($invoiceModel->isInvoice() && $this->isLessonItem()) {
            if ($this->lesson->isPrivate()) {
                if ($this->lesson->hasCreditUsed($this->enrolment->id)) {
                    $lessonCreditPayments = $this->lesson->getCreditUsedPayment($this->enrolment->id);
                    foreach ($lessonCreditPayments as $lessonCreditPayment) {
                        $lessonCreditPayment->delete();
                    }
                }
                if ($this->lessonCanBeUnscheduled) {
                    $this->lesson->unschedule();
                }
            }
        }
        $invoiceModel->save();
        return true;
    }
    
    public function isDefaultDiscountedItems()
    {
        return $this->isMisc() || $this->isOpeningBalance();
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            if ($this->isDefaultDiscountedItems()) {
                $this->rate         = 0;
            }
            $taxStatus         = TaxStatus::findOne(['id' => $this->item->taxStatusId]);
            $this->code        = $this->item->code;
            $this->tax_type    = $taxStatus->taxTypeTaxStatusAssoc->taxType->name;
            $this->tax_rate    = $this->netPrice * $taxStatus->taxTypeTaxStatusAssoc->taxType->taxCode->rate / 100.0;
            $this->tax_code    = $taxStatus->taxTypeTaxStatusAssoc->taxType->taxCode->code;
            $this->tax_status  = $taxStatus->name;
            $this->netTotal = $this->netPrice;
            if (!isset($this->royaltyFree)) {
                $this->royaltyFree = $this->item->royaltyFree;
            }
            $this->isDeleted = false;
        }

        if (!$insert) {
            $taxStatus = TaxStatus::findOne(['name' => $this->tax_status]);
            $this->tax_type = $taxStatus->taxTypeTaxStatusAssoc->taxType->name;
            $this->tax_rate = $this->netPrice * $taxStatus->taxTypeTaxStatusAssoc->taxType->taxCode->rate / 100.0;
            $this->netTotal = $this->netPrice;
        }
        return parent::beforeSave($insert);
    }
    
    public function computeTaxCode($taxStatus)
    {
        $today         = (new \DateTime())->format('Y-m-d H:i:s');
        $locationId    = Location::findOne(['slug' => \Yii::$app->location])->id;
        $locationModel = Location::findOne(['id' => $locationId]);
        $taxCode = TaxCode::find()
            ->joinWith(['taxStatus' => function ($query) use ($taxStatus) {
                $query->andWhere(['tax_status.id' => $taxStatus]);
            }])
            ->andWhere(['<=', 'start_date', $today])
            ->andWhere(['province_id' => $locationModel->province_id])
            ->orderBy('start_date DESC')
            ->one();
            
        return $taxCode;
    }
    
    public function getTaxPercentage()
    {
        return $this->taxType->taxCode->rate;
    }
    
    public function canApplyCustomerDiscount()
    {
        return $this->invoice->user->hasDiscount() && !$this->invoice->isReversedInvoice()
                && !$this->isOpeningBalance() && !$this->isGroupLesson();
    }

    public function afterSave($insert, $changedAttributes)
    {
        if (!$insert) {
            $this->invoice->save();
        } else {
            if ($this->invoice->user) {
                if ($this->canApplyCustomerDiscount()) {
                    $this->addCustomerDiscount($this->invoice->user);
                }
            }
        }
        return parent::afterSave($insert, $changedAttributes);
    }

    public function getTaxType()
    {
        return $this->hasOne(TaxType::className(), ['name' => 'tax_type']);
    }

    public function isSpecialLineItems()
    {
        return $this->isOpeningBalance() || $this->isLessonCredit();
    }

    public function isLessonCredit()
    {
        return (int) $this->item_type_id === (int) ItemType::TYPE_LESSON_CREDIT;
    }

    public function isOpeningBalance()
    {
        return (int) $this->item_type_id === (int) ItemType::TYPE_OPENING_BALANCE;
    }

    public function isMisc()
    {
        return (int) $this->item_type_id === (int) ItemType::TYPE_MISC;
    }

    public function isPrivateLesson()
    {
        return (int) $this->item_type_id === (int) ItemType::TYPE_PRIVATE_LESSON;
    }

    public function isGroupLesson()
    {
        return (int) $this->item_type_id === (int) ItemType::TYPE_GROUP_LESSON;
    }

    public function getLineItemDiscountValue()
    {
        $discount = 0;
        $lineItemPrice = $this->grossPrice;
        if ($this->hasMultiEnrolmentDiscount()) {
            $discount += $lineItemPrice < 0 ? - ($this->multiEnrolmentDiscount->value) :
                $this->multiEnrolmentDiscount->value;
            $lineItemPrice -= $discount;
        }
        if ((int) $this->lineItemDiscount->valueType) {
            return $this->lineItemDiscount->value;
        } else {
            return ($this->lineItemDiscount->value / 100) * $lineItemPrice;
        }
    }

    public function getItemTotal()
    {
        return $this->netPrice + $this->tax_rate;
    }

    public function getNetPrice()
    {
        return $this->grossPrice - $this->discount;
    }
    
    public function getGrossPrice()
    {
        return $this->isGroupLesson() ? round($this->amount, 2) :
            round($this->amount * $this->unit, 2);
    }

    public function getDiscount()
    {
        $discount = 0.0;
        $lineItemPrice = $this->grossPrice;
        if ($this->hasMultiEnrolmentDiscount()) {
            $discount += $lineItemPrice < 0 ? - ($this->multiEnrolmentDiscount->value) :
                $this->multiEnrolmentDiscount->value;
            $lineItemPrice = $this->grossPrice - round($discount, 2);
        }
        if ($this->hasLineItemDiscount()) {
            if ((int) $this->lineItemDiscount->valueType) {
                $discount += ($this->lineItemDiscount->value / 100) * $lineItemPrice;
            } else {
                $discount += $lineItemPrice < 0 ? - ($this->lineItemDiscount->value) :
                    $this->lineItemDiscount->value;
            }
            $lineItemPrice = $this->grossPrice - round($discount, 2);
        }
        if ($this->hasGroupDiscount()) {
            if ((int) $this->groupDiscount->valueType) {
                $discount += ($this->groupDiscount->value / 100) * $lineItemPrice;
            } else {
                $discount += $lineItemPrice < 0 ? - ($this->groupDiscount->value) :
                    $this->groupDiscount->value;
            }
            $lineItemPrice = $this->grossPrice - round($discount, 2);
        }
        if ($this->hasCustomerDiscount()) {
            $discount += ($this->customerDiscount->value / 100) * $lineItemPrice;
            $lineItemPrice = $this->grossPrice - round($discount, 2);
        }
        if ($this->hasEnrolmentPaymentFrequencyDiscount()) {
            $discount += round(($this->enrolmentPaymentFrequencyDiscount->value / 100) * $lineItemPrice, 2);
        }
        
        return round($discount, 2);
    }
    
    public function getTaxLineItemTotal($date)
    {
        $locationId = $this->invoice->location_id;
        $taxTotal = self::find()
                        ->notDeleted()
            ->taxRateSummary($date, $locationId)
            ->sum('tax_rate');

        return $taxTotal;
    }

    public function getTaxLineItemAmount($date)
    {
        $locationId = $this->invoice->location_id;
        $amount = self::find()
                        ->notDeleted()
            ->taxRateSummary($date, $locationId)
            ->sum('amount');

        return $amount;
    }

    public function getTotal($date)
    {
        $locationId = $this->invoice->location_id;
        $total = self::find()
                        ->notDeleted()
            ->taxRateSummary($date, $locationId)
            ->sum('amount+tax_rate');

        return $total;
    }
    
    public function isExtraLesson()
    {
        return (int) $this->item_type_id === (int) ItemType::TYPE_EXTRA_LESSON;
    }

    public function isPaymentCycleLesson()
    {
        return (int) $this->item_type_id === (int) ItemType::TYPE_PAYMENT_CYCLE_PRIVATE_LESSON;
    }

    public function getLessonCreditUnit($lesson)
    {
        $getDuration = \DateTime::createFromFormat('H:i:s', $lesson->duration);
        $hours       = $getDuration->format('H');
        $minutes     = $getDuration->format('i');
        return (($hours * 60) + $minutes) / 60;
    }
    
    public function getLessonDuration($date, $teacherId)
    {
        $totalDuration = InvoiceLineItem::find()
                        ->notDeleted()
            ->joinWith(['invoice' => function ($query) use ($date, $teacherId) {
                $query->andWhere(['invoice.isDeleted' => false, 'invoice.type' => Invoice::TYPE_INVOICE])
                    ->between($date, $date);
            }])
            ->joinWith(['lesson' => function ($query) use ($teacherId) {
                $query->andWhere(['lesson.teacherId' => $teacherId]);
            }])
            ->sum('unit');
        return $totalDuration;
    }

    public function getLessonCost($date, $teacherId)
    {
        $totalCost = InvoiceLineItem::find()
                        ->notDeleted()
            ->joinWith(['invoice' => function ($query) use ($date, $teacherId) {
                $query->andWhere(['invoice.isDeleted' => false, 'invoice.type' => Invoice::TYPE_INVOICE])
                    ->between($date, $date);
            }])
            ->joinWith(['lesson' => function ($query) use ($teacherId) {
                $query->andWhere(['lesson.teacherId' => $teacherId]);
            }])
            ->sum('cost');
        return $totalCost;
    }

    public function addLessonCreditApplied($lesson)
    {
        $old = clone $this;
        $this->unit   = $this->unit + $this->getLessonCreditUnit($lesson);
        $this->amount = $lesson->enrolment->program->rate;
        $this->save();
        $creditUsedInvoice = $lesson->invoice;
        if (!$lesson->hasInvoice()) {
            $creditUsedInvoice = $lesson->proFormaInvoice;
        }
        if ($creditUsedInvoice) {
            if ($this->invoice->addLessonCreditAppliedPayment($this->itemTotal - $old->itemTotal, $creditUsedInvoice)) {
                $creditUsedInvoice->save();
            }
        }
    }

    public function addLineItemDetails($lesson)
    {
        if ($this->invoice->isReversedInvoice()) {
            return true;
        }
        if ($this->isPrivateLesson() || $this->isExtraLesson() ||
            $this->isGroupLesson()) {
            $invoiceItemLesson = new InvoiceItemLesson();
            $invoiceItemLesson->lessonId    = $lesson->id;
            $invoiceItemLesson->invoiceLineItemId    = $this->id;
            $invoiceItemLesson->save();
        }
        if (!$this->isGroupLesson()) {
            if ($this->invoice->isInvoice()) {
                if ($lesson->hasCustomerDiscount()) {
                    $customerDiscount = $this->addCustomerDiscount(
                        null,
                            $lesson->customerDiscount
                    );
                }
                if ($lesson->hasLineItemDiscount()) {
                    $lineItemDiscount = $this->addLineItemDiscount(
                            $lesson->lineItemDiscount
                    );
                }
                if ($lesson->hasEnrolmentPaymentFrequencyDiscount()) {
                    $pfDiscount = $this->addEnrolmentPaymentFrequencyDiscount(
                        null, $lesson->enrolmentPaymentFrequencyDiscount);
                }
                if ($lesson->hasMultiEnrolmentDiscount()) {
                    $enrolDiscount = $this->addMultiEnrolmentDiscount(
                        null, $lesson->multiEnrolmentDiscount);
                }
            }
        }
    }

    public function hasLineItemDiscount()
    {
        return !empty($this->lineItemDiscount);
    }

    public function hasCustomerDiscount()
    {
        return !empty($this->customerDiscount);
    }

    public function hasGroupDiscount()
    {
        return !empty($this->groupDiscount);
    }

    public function hasEnrolmentPaymentFrequencyDiscount()
    {
        return !empty($this->enrolmentPaymentFrequencyDiscount);
    }

    public function hasMultiEnrolmentDiscount()
    {
        return !empty($this->multiEnrolmentDiscount);
    }

    public function addLineItemDiscount($lineItemDiscount)
    {
        $invoiceLineItemDiscount            = new InvoiceLineItemDiscount();
        $invoiceLineItemDiscount->invoiceLineItemId = $this->id;
        $invoiceLineItemDiscount->value     = $lineItemDiscount->value;
        $invoiceLineItemDiscount->valueType = $lineItemDiscount->valueType;
        $invoiceLineItemDiscount->type      = $lineItemDiscount->type;
        return $invoiceLineItemDiscount->save();
    }

    public function addGroupDiscount($groupDiscount)
    {
        $invoiceLineItemDiscount            = new InvoiceLineItemDiscount();
        $invoiceLineItemDiscount->invoiceLineItemId = $this->id;
        $invoiceLineItemDiscount->value     = $groupDiscount->value;
        $invoiceLineItemDiscount->valueType = $groupDiscount->valueType;
        $invoiceLineItemDiscount->type      = InvoiceLineItemDiscount::TYPE_GROUP;
        return $invoiceLineItemDiscount->save();
    }

    public function addCustomerDiscount($user = null, $discount = null)
    {
        $invoiceLineItemDiscount            = new InvoiceLineItemDiscount();
        $invoiceLineItemDiscount->invoiceLineItemId = $this->id;
        $invoiceLineItemDiscount->valueType = InvoiceLineItemDiscount::VALUE_TYPE_PERCENTAGE;
        $invoiceLineItemDiscount->type      = InvoiceLineItemDiscount::TYPE_CUSTOMER;
        if ($user) {
            $invoiceLineItemDiscount->value     = $user->customerDiscount->value;
        } else {
            $invoiceLineItemDiscount->value = $discount->value;
        }
        return $invoiceLineItemDiscount->save();
    }

    public function addEnrolmentPaymentFrequencyDiscount($enrolment = null, $discount = null)
    {
        $invoiceLineItemDiscount            = new InvoiceLineItemDiscount();
        $invoiceLineItemDiscount->invoiceLineItemId = $this->id;
        if (!$enrolment) {
            $invoiceLineItemDiscount->value     = $discount->value;
        } else {
            $invoiceLineItemDiscount->value     = $enrolment->paymentFrequencyDiscount->discount;
        }
        $invoiceLineItemDiscount->valueType = InvoiceLineItemDiscount::VALUE_TYPE_PERCENTAGE;
        $invoiceLineItemDiscount->type      = InvoiceLineItemDiscount::TYPE_ENROLMENT_PAYMENT_FREQUENCY;
        return $invoiceLineItemDiscount->save();
    }

    public function addMultiEnrolmentDiscount($enrolment = null, $discount = null)
    {
        $invoiceLineItemDiscount            = new InvoiceLineItemDiscount();
        $invoiceLineItemDiscount->invoiceLineItemId = $this->id;
        if (!$enrolment) {
            $invoiceLineItemDiscount->value     = $discount->value;
        } else {
            $invoiceLineItemDiscount->value     = $enrolment->multipleEnrolmentDiscount->discountPerLesson;
        }
        $invoiceLineItemDiscount->value = $invoiceLineItemDiscount->value;
        $invoiceLineItemDiscount->valueType = InvoiceLineItemDiscount::VALUE_TYPE_DOLLAR;
        $invoiceLineItemDiscount->type      = InvoiceLineItemDiscount::TYPE_MULTIPLE_ENROLMENT;
        return $invoiceLineItemDiscount->save();
    }
    
    public function addFullDiscount()
    {
        $discount = new InvoiceLineItemDiscount();
        $discount->type = InvoiceLineItemDiscount::TYPE_ENROLMENT_PAYMENT_FREQUENCY;
        $discount->invoiceLineItemId = $this->id;
        $discount->valueType = InvoiceLineItemDiscount::VALUE_TYPE_PERCENTAGE;
        $discount->value = InvoiceLineItemDiscount::FULL_DISCOUNT;
        return $discount->save();
    }

    public static function getItemCategoryName($itemCategoryId)
    {
        $itemcategory = ItemCategory::findOne(['id' => $itemCategoryId]);
        return $itemcategory->name;
    }
}
