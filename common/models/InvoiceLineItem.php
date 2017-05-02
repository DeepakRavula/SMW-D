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
    const DISCOUNT_FLAT            = 0;
    const DISCOUNT_PERCENTAGE      = 1;

    const EVENT_EDIT = 'edit';
    const EVENT_DELETE = 'deleteLineItem';
    
    const STATUS_DEFAULT = 'Default';
    const STATUS_NO_TAX = 'No Tax';
    const STATUS_GST_ONLY = 'GST Only';

    public $userName;
    public $price;
    public $tax;
    
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
            [['unit', 'amount', 'description', 'tax_status'], 'required', 'when' => function ($model, $attribute) {
                return (int) $model->item_type_id === ItemType::TYPE_MISC;
            },
            ],
            [['amount'], 'number', 'when' => function ($model, $attribute) {
                return (int) $model->item_type_id === ItemType::TYPE_MISC;
            },
            ],
            ['amount', 'compare', 'operator' => '>', 'compareValue' => 0, 'except' => self::SCENARIO_OPENING_BALANCE],
            [['isRoyaltyExempted'], 'boolean', 'when' => function ($model, $attribute) {
                return (int) $model->item_type_id === ItemType::TYPE_MISC;
            },
            ],
            [['unit'], 'integer', 'when' => function ($model, $attribute) {
                return (int) $model->item_type_id === ItemType::TYPE_MISC;
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
        return $this->hasOne(Lesson::className(), ['id' => 'item_id']);
    }
	
    public function getPaymentCycleLesson()
    {
        return $this->hasOne(PaymentCycleLesson::className(), ['id' => 'item_id']);
    }

    public function getItemType()
    {
        return $this->hasOne(ItemType::className(), ['id' => 'item_type_id']);
    }
    
    public function getTaxStatus()
    {
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

    public function getInvoice()
    {
        return $this->hasOne(Invoice::className(), ['id' => 'invoice_id']);
    }

    public function getProFormaLesson()
    {
        return $this->hasOne(Lesson::className(), ['id' => 'lessonId'])
                    ->viaTable('payment_cycle_lesson', ['id' => 'item_id']);
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
            'isRoyalty' => 'Royalty',
			'tax' => 'Tax (%)'
        ];
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            if ($this->isMisc()) {
                $taxStatus         = TaxStatus::findOne(['id' => $this->tax_status]);
                $this->tax_status  = $taxStatus->name;
                $this->discount     = 0.0;
                $this->discountType = 0;
            } else  {
                $taxStatus          = TaxStatus::findOne(['id' => TaxStatus::STATUS_NO_TAX]);
                $this->tax_type     = $taxStatus->taxTypeTaxStatusAssoc->taxType->name;
                $this->tax_rate     = 0.0;
                $this->tax_code     = $taxStatus->taxTypeTaxStatusAssoc->taxType->taxCode->code;
                $this->tax_status   = $taxStatus->name;
                $this->isRoyalty    = true;
				}
            if ($this->isOpeningBalance()) {
				$this->discount     = 0.0;
                $this->discountType = 0;
                $this->isRoyalty  = false;
            }
			if($this->isLessonCredit()) {
        		return parent::beforeSave($insert);
			}
        }
        
        if (!$insert) {
            if ($this->invoice->isReversedInvoice()) {
                $this->amount = -($this->amount);
                $this->setScenario(self::SCENARIO_OPENING_BALANCE);
            } else if ($this->invoice->isProFormaInvoice()) {
                if ((int) $this->proFormaLesson->course->program->type === (int) Program::TYPE_GROUP_PROGRAM) {
                    $courseCount  = Lesson::find()
                        ->where(['courseId' => $this->proFormaLesson->courseId])
                        ->count('id');
                    $this->amount = $this->proFormaLesson->course->program->rate / $courseCount;
                } else {
                    $this->amount = $this->proFormaLesson->enrolment->program->rate * $this->unit;
                }
            } else if ($this->invoice->isInvoice()) {
                if ((int) $this->lesson->course->program->type === (int) Program::TYPE_GROUP_PROGRAM) {
                    $courseCount  = Lesson::find()
                        ->where(['courseId' => $this->lesson->courseId])
                        ->count('id');
                    $this->amount = $this->lesson->course->program->rate / $courseCount;
                } else {
                    $this->amount = $this->lesson->enrolment->program->rate * $this->unit;
                }
            }
            $this->tax_rate = $this->amount * $this->taxType->taxCode->rate / 100;
        }
        return parent::beforeSave($insert);
    }
    
    public function getCost()
    {
        $cost = 0;
        $itemTypes = [ItemType::TYPE_PAYMENT_CYCLE_PRIVATE_LESSON, ItemType::TYPE_GROUP_LESSON];
        if(in_array($this->item_type_id,$itemTypes)) {
            if((int)$this->item_type_id === ItemType::TYPE_PAYMENT_CYCLE_PRIVATE_LESSON) {
                $cost = !empty($this->paymentCycleLesson->lesson->teacher->teacherPrivateLessonRate->hourlyRate) ? $this->paymentCycleLesson->lesson->teacher->teacherPrivateLessonRate->hourlyRate : null;
            } else {
                $cost = !empty($this->paymentCycleLesson->lesson->teacher->teacherGroupLessonRate->hourlyRate) ? $this->paymentCycleLesson->lesson->teacher->teacherGroupLessonRate->hourlyRate : null;
            }
        }
        return $cost;
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
}
