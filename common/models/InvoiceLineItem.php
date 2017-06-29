<?php

namespace common\models;

use Yii;
use common\models\ItemType;
use common\models\TaxStatus;
use common\models\query\InvoiceLineItemQuery;
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
    private $isRoyaltyExempted;
   
    const SCENARIO_OPENING_BALANCE = 'allow-negative-line-item-amount';
    const SCENARIO_LINE_ITEM_CREATE = 'line-item-create';
    const SCENARIO_EDIT = 'edit';
    const DISCOUNT_FLAT            = 0;
    const DISCOUNT_PERCENTAGE      = 1;

    const EVENT_EDIT = 'edit';
    const EVENT_DELETE = 'deleteLineItem';
    const EVENT_CREATE= 'newLineItem';
    
    const STATUS_DEFAULT = 'Default';
    const STATUS_NO_TAX = 'No Tax';
    const STATUS_GST_ONLY = 'GST Only';

    public $userName;
    public $price;
    public $tax;
    public $itemCategoryId;
    public $itemId;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'invoice_line_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['taxStatus', 'required', 'on' => self::SCENARIO_EDIT],
            ['code', 'required'],
            [['unit', 'amount', 'item_id', 'description', 'tax_status'],
                'required', 'when' => function ($model, $attribute) {
                return (int) $model->item_type_id === ItemType::TYPE_MISC;
            }],
            ['itemCategoryId', 'required', 'when' => function ($model, $attribute) {
                return (int) $model->item_type_id === ItemType::TYPE_MISC;
            }, 'on' => self::SCENARIO_LINE_ITEM_CREATE],
            [['amount'], 'number', 'when' => function ($model, $attribute) {
                return (int) $model->item_type_id === ItemType::TYPE_MISC;
            },
            ],
            ['amount', 'compare', 'operator' => '>', 'compareValue' => 0, 'except' => self::SCENARIO_OPENING_BALANCE],
            [['isRoyaltyExempted'], 'boolean', 'when' => function ($model, $attribute) {
                return (int) $model->item_type_id === ItemType::TYPE_MISC;
            },
            ],
            [['unit'], 'number', 'when' => function ($model, $attribute) {
                return (int) $model->item_type_id !== ItemType::TYPE_MISC;
            },
            ],
            [['isRoyalty', 'invoice_id', 'item_id', 'item_type_id', 'tax_code',
                'tax_status', 'tax_type', 'tax_rate', 'userName', 'discount', 
                'discountType', 'cost', 'code'], 'safe'],
        ];
    }

    public static function find()
    {
        return new InvoiceLineItemQuery(get_called_class());
    }

    public function getLesson()
    {
        $query = $this->hasOne(Lesson::className(), ['id' => 'lessonId']);
        if($this->isLessonSplit()) {
            return $query->via('lineItemPaymentCycleLessonSplit');
        } else if($this->isPaymentCycleLesson()) {
            return $query->via('paymentCycleLesson');
        } else {
            return $query->via('lineItemLesson');
        }
    }
	
    public function getPaymentCycleLesson()
    {
        return $this->hasOne(PaymentCycleLesson::className(), ['id' => 'paymentCycleLessonId'])
                ->via('lineItemPaymentCycleLesson');
    }

    public function getItemType()
    {
        return $this->hasOne(ItemType::className(), ['id' => 'item_type_id']);
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

    public function getEnrolment()
    {
        if($this->isGroupLesson()) {
            return $this->hasOne(Enrolment::className(), ['id' => 'enrolmentId'])
                ->via('lineItemEnrolment');
        } else {
            return $this->hasOne(Enrolment::className(), ['courseId' => 'courseId'])
                ->via('lesson');
        }
    }

    public function getLineItemEnrolment()
    {
        return $this->hasOne(InvoiceItemEnrolment::className(), ['invoiceLineItemId' => 'id']);
    }

    public function getLineItemPaymentCycleLesson()
    {
        return $this->hasOne(InvoiceItemPaymentCycleLesson::className(), ['invoiceLineItemId' => 'id']);
    }

    public function getLineItemPaymentCycleLessonSplit()
    {
        return $this->hasOne(InvoiceItemPaymentCycleLessonSplit::className(), ['invoiceLineItemId' => 'id']);
    }

    public function getLessonSplit()
    {
        return $this->hasOne(LessonSplit::className(), ['id' => 'lessonSplitId'])
            ->via('lineItemPaymentCycleLessonSplit');
    }

    public function getInvoice()
    {
        return $this->hasOne(Invoice::className(), ['id' => 'invoice_id']);
    }

    public function getProFormaLesson()
    {
        if ($this->isPaymentCycleLesson()) {
            return $this->hasOne(Lesson::className(), ['id' => 'lessonId'])
                    ->via('paymentCycleLesson');
        } else if($this->isPaymentCycleLessonSplit()) {
            return $this->hasOne(Lesson::className(), ['id' => 'lessonId'])
                    ->via('lessonSplit');
        }
    }

    public function getOriginalInvoice()
    {
        return $this->hasOne(Invoice::className(), ['id' => 'invoice_id'])
                ->where(['invoice.type' => Invoice::TYPE_INVOICE]);
    }


    public function getIsRoyaltyExempted()
    {
        return $this->isRoyaltyExempted;
    }

    public function setIsRoyaltyExempted($isRoyaltyExempted)
    {
        $this->isRoyalty = !$isRoyaltyExempted;
    }

    public function getIsRoyalty()
    {
        return $this->isRoyalty;
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
            'amount' => 'Total',
            'discount' => 'Discount',
            'discountType' => 'Discount Type',
            'description' => 'Description',
            'tax_rate' => 'Tax',
            'tax_status' => 'Tax Status',
            'isRoyaltyExempted' => 'Exempt from Royalty',
            'isRoyalty' => 'Royalty Free?',
            'tax' => 'Tax (%)',
            'itemCategoryId' => 'Item Category',
            'item_id' => 'Item',
        ];
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            if ($this->isOpeningBalance()) {
                $this->discount     = 0.0;
                $this->discountType = 0;
                $this->isRoyalty  = false;
            } else if (!$this->isMisc() && !$this->isLessonCredit()) {
                $taxStatus          = TaxStatus::findOne(['id' => TaxStatus::STATUS_NO_TAX]);
                $this->tax_type     = $taxStatus->taxTypeTaxStatusAssoc->taxType->name;
                $this->tax_rate     = 0.0;
                $this->tax_code     = $taxStatus->taxTypeTaxStatusAssoc->taxType->taxCode->code;
                $this->tax_status   = $taxStatus->name;
                $this->isRoyalty    = true;
            }
        }
        
        if (!$insert) {
            if ($this->invoice->isReversedInvoice()) {
                $this->amount = -($this->amount);
                $this->setScenario(self::SCENARIO_OPENING_BALANCE);
            }
            $taxType = TaxType::findOne(['name' => $this->tax_type]);
            $this->tax_rate = $this->amount * $taxType->taxCode->rate / 100.0;
        }
        return parent::beforeSave($insert);
    }
    
    public function computeTaxCode($taxStatus)
    {
        $today         = (new \DateTime())->format('Y-m-d H:i:s');
        $locationId    = Yii::$app->session->get('location_id');
        $locationModel = Location::findOne(['id' => $locationId]);
        $taxCode = TaxCode::find()
            ->joinWith(['taxStatus' => function ($query) use ($taxStatus) {
                $query->where(['tax_status.id' => $taxStatus]);
            }])
            ->where(['<=', 'start_date', $today])
            ->andWhere(['province_id' => $locationModel->province_id])
            ->orderBy('start_date DESC')
            ->one();
            
        return $taxCode;
    }
    
    public function getTaxPercentage()
    {
        return $this->taxType->taxCode->rate;
    }

    public function afterSave($insert, $changedAttributes)
    {
        if (!$insert) {
            $this->invoice->save();
        }
        
        return parent::afterSave($insert, $changedAttributes);
    }

    public function getTaxType()
    {
        return $this->hasOne(TaxType::className(), ['name' => 'tax_type']);
    }

    public function isOtherLineItems()
    {
        $isOtherLineItems = (int) $this->item_type_id === (int) ItemType::TYPE_MISC ||
            (int) $this->item_type_id === (int) ItemType::TYPE_PRIVATE_LESSON ||
            (int) $this->item_type_id === (int) ItemType::TYPE_GROUP_LESSON;
        return $isOtherLineItems;
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

    public function isLessonSplit()
    {
        return (int) $this->item_type_id === (int) ItemType::TYPE_LESSON_SPLIT;
    }

    public function isGroupLesson()
    {
        return (int) $this->item_type_id === (int) ItemType::TYPE_GROUP_LESSON;
    }

    public function getDiscountValue()
    {
        if ((int) $this->discountType === (int) self::DISCOUNT_FLAT) {
            return $this->discount;
        } else {
            return ($this->discount / 100) * $this->amount;
        }
    }

	public function getNetPrice()
    {
        return $this->amount - $this->discountValue;
    }
	
	public function getTaxLineItemTotal($date)
    {
		$locationId = $this->invoice->location_id;
		$taxTotal = self::find()
        	->taxRate($date, $locationId)
            ->sum('tax_rate');

		return $taxTotal;
    }

	public function getTaxLineItemAmount($date)
    {
		$locationId = $this->invoice->location_id;
		$amount = self::find()
        	->taxRate($date, $locationId)
            ->sum('amount');

		return $amount;
    }

	public function getTotal($date)
    {
		$locationId = $this->invoice->location_id;
		$total = self::find()
        	->taxRate($date, $locationId)
            ->sum('amount+tax_rate');

		return $total;
    }
	public function getRoyalty()
    {
		$royalty = 'No';
		if($this->isRoyalty) {
			$royalty = 'Yes';
		}

		return $royalty;
    }

    public function getLessonCreditAmount($splitId)
    {
        $lesson = Lesson::find()
                    ->joinWith('lessonSplits')
                    ->andWhere(['lesson_split.id' => $splitId])
                    ->one();
        return $lesson->enrolment->program->rate * $this->unit;
    }

    public function isExtraLesson()
    {
        return (int) $this->item_type_id === (int) ItemType::TYPE_EXTRA_LESSON;
    }

    public function isPaymentCycleLesson()
    {
        return (int) $this->item_type_id === (int) ItemType::TYPE_PAYMENT_CYCLE_PRIVATE_LESSON;
    }

    public function isPaymentCycleLessonSplit()
    {
        return (int) $this->item_type_id === (int) ItemType::TYPE_LESSON_SPLIT;
    }

    public function getLessonCreditUnit($splitId)
    {
        $split       = LessonSplit::findOne($splitId);
        $getDuration = \DateTime::createFromFormat('H:i:s', $split->unit);
        $hours       = $getDuration->format('H');
        $minutes     = $getDuration->format('i');
        return (($hours * 60) + $minutes) / 60;
    }
	public function getLessonDuration($date, $teacherId)
	{
		$totalDuration = InvoiceLineItem::find()
			->joinWith(['invoice' => function($query) use($date, $teacherId) {
				$query->andWhere(['invoice.isDeleted' => false, 'invoice.type' => Invoice::TYPE_INVOICE])
					->between($date, $date);
			}])
			->joinWith(['lesson' => function($query) use($teacherId){
				$query->andWhere(['lesson.teacherId' => $teacherId]);
			}])
			->sum('unit');
			return $totalDuration;
	}
	public function getLessonCost($date, $teacherId)
	{
		$totalCost = InvoiceLineItem::find()
			->joinWith(['invoice' => function($query) use($date, $teacherId) {
				$query->andWhere(['invoice.isDeleted' => false, 'invoice.type' => Invoice::TYPE_INVOICE])
					->between($date, $date);
			}])
			->joinWith(['lesson' => function($query) use($teacherId){
				$query->andWhere(['lesson.teacherId' => $teacherId]);
			}])
			->sum('cost');
			return $totalCost;
	}
    public function addLessonCreditApplied($splitId)
    {
        $lessonSplit  = LessonSplit::findOne($splitId);
        $old = clone $this;
        $this->unit   = $this->unit + $this->getLessonCreditUnit($splitId);
        $amount = $this->lesson->enrolment->program->rate * $this->unit;
        $this->amount = $amount;
        $this->save();
        $creditUsedInvoice = $lessonSplit->lesson->invoice;
        if (!$lessonSplit->lesson->hasInvoice()) {
            $creditUsedInvoice = $lessonSplit->lesson->proFormaInvoice;
        }
        if ($this->invoice->addLessonCreditAppliedPayment($this->netPrice - $old->netPrice, $creditUsedInvoice)) {
            $creditUsedInvoice->save();
        }
    }

    public function beforeDelete()
    {
        if ($this->isPaymentCycleLesson()) {
            $this->lineItemPaymentCycleLesson->delete();
        } else if ($this->isPrivateLesson() || $this->isExtraLesson()) {
            $this->lineItemLesson->delete();
        }

        return parent::beforeDelete();
    }

    public function addLineItemDetails($lesson)
    {
        if ($this->isPaymentCycleLesson()) {
            $invoiceItemPaymentCycleLesson = new InvoiceItemPaymentCycleLesson();
            $invoiceItemPaymentCycleLesson->paymentCycleLessonId    = $lesson->paymentCycleLesson->id;
            $invoiceItemPaymentCycleLesson->invoiceLineItemId    = $this->id;
            $invoiceItemPaymentCycleLesson->save();
        }
        if ($this->isPaymentCycleLessonSplit()) {
            $invoiceItemPaymentCycleLesson = new InvoiceItemPaymentCycleLessonSplit();
            $invoiceItemPaymentCycleLesson->lessonSplitId     = $lesson->id;
            $invoiceItemPaymentCycleLesson->invoiceLineItemId = $this->id;
            return $invoiceItemPaymentCycleLesson->save();
        }
        if ($this->isPrivateLesson() || $this->isExtraLesson() ||
            ($this->isGroupLesson() && $this->invoice->isInvoice())) {
            $invoiceItemLesson = new InvoiceItemLesson();
            $invoiceItemLesson->lessonId    = $lesson->id;
            $invoiceItemLesson->invoiceLineItemId    = $this->id;
            $invoiceItemLesson->save();
        }
    }
}
