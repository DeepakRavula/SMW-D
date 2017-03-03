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
            [['isRoyalty', 'invoice_id', 'item_id', 'item_type_id', 'tax_code', 'tax_status', 'tax_type', 'tax_rate'], 'safe'],
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

    public function getItemType()
    {
        return $this->hasOne(ItemType::className(), ['id' => 'item_type_id']);
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
            $this->tax_rate = $this->amount * $this->taxType->taxCode->rate / 100;
        }
        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        $this->invoice->save();
        
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
}
