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
 * @property integer $lesson_id
 * @property double $unit
 * @property string $amount
 * @property string $tax
 */
class InvoiceLineItem extends \yii\db\ActiveRecord
{
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
            [['invoice_id', 'lesson_id', 'unit', 'amount', 'tax'], 'required'],
            [['invoice_id', 'lesson_id'], 'integer'],
            [['unit', 'amount', 'tax'], 'number'],
        ];
    }

    public function getLesson()
    {
        return $this->hasOne(Lesson::className(), ['id' => 'lesson_id']);
    }

    public function getInvoice()
    {
        return $this->hasOne(Invoice::className(), ['invoice_id' => 'id']);
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
            'unit' => 'Unit',
            'amount' => 'Amount',
            'tax' => 'Tax',
        ];
    }
}
