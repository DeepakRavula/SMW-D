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
	public $isTax;
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
            [['invoice_id', 'unit', 'amount','item_id','item_type_id','description'], 'required'],
            [['invoice_id', 'item_id'], 'integer'],
            [['unit', 'amount'], 'number'],
        ];
    }

    public function getLesson()
    {
        return $this->hasOne(Lesson::className(), ['id' => 'item_id']);
    }

    public function getInvoice()
    {
        return $this->hasOne(Invoice::className(), ['id' => 'invoice_id']);
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
            'amount' => 'Amount',
			'description' => 'Description'
        ];
    }
}
