<?php

namespace common\models;

use common\models\ItemType;
use common\models\TaxStatus;
/**
 * This is the model class for table "invoice_line_item".
 *
 * @property int $id
 * @property int $invoice_id
 * @property int $item_id
 * @property float $unit
 * @property string $amount
 */
class InvoiceLineItem extends \yii\db\ActiveRecord
{
    private $isRoyaltyExempted;

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
            'description' => 'Description',
            'tax_rate' => 'Tax',
            'tax_status' => 'Tax Status',
            'isRoyaltyExempted' => 'Exempt from Royalty',
        ];
    }

    public function beforeSave($insert)
    {
        if (!$this->isMisc()) {
            $taxStatus        = TaxStatus::findOne(['id' => TaxStatus::STATUS_NO_TAX]);
            $this->tax_type   = $taxStatus->taxTypeTaxStatusAssoc->taxType->name;
            $this->tax_rate   = 0.0;
            $this->tax_code   = $taxStatus->taxTypeTaxStatusAssoc->taxType->taxCode->code;
            $this->tax_status = $taxStatus->name;
            $this->isRoyalty  = true;
            if($this->isOpeningBalance()) {
                $this->isRoyalty  = false;
            }
        }
        if(!$insert) {
            $this->tax_rate = $this->amount * $this->taxType->taxCode->rate / 100;
        }
        return parent::beforeSave($insert);
    }

    public function getTaxType()
    {
        return $this->hasOne(TaxType::className(), ['name' => 'tax_type']);
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
}
