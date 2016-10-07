<?php

namespace common\models;

use Yii;
use common\models\Invoice;
use common\models\Lesson;

/**
 * This is the model class for table "invoice_line_item".
 *
 * @property integer $id
 * @property integer $invoice_id
 * @property integer $item_id
 * @property double $unit
 * @property string $amount
 */
class InvoiceLineItem extends \yii\db\ActiveRecord
{
	private $isRoyaltyExempted;
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'invoice_line_item';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['invoice_id', 'unit', 'amount','item_id','item_type_id', 'tax_code', 'tax_status', 'tax_type', 'tax_rate', 'description'], 'required'],
            [['invoice_id', 'item_id'], 'integer'],
            [['unit', 'amount'], 'number'],
			[['isRoyalty'], 'safe'],
			[['isRoyaltyExempted'], 'boolean'],
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
    	$this->isRoyalty = ! $isRoyaltyExempted;
	}

	public function getIsRoyalty() 
	{
    	return $this->isRoyalty;
	}
    /**
     * @inheritdoc
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
			'isRoyaltyExempted' => 'Exempt from Royalty'
        ];
    }
}
