<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "invoice_item_lesson".
 *
 * @property string $id
 * @property string $itemId
 * @property string $productId
 */
class InvoiceItemLesson extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'invoice_item_lesson';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['invoiceLineItemId', 'lessonId'], 'required'],
            [['invoiceLineItemId', 'lessonId'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'invoiceLineItemId' => 'Invoice Line Item ID',
            'lessonId' => 'Lesson ID',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\InvoiceItemLessonQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\InvoiceItemLessonQuery(get_called_class());
    }

    public function getInvoiceLineItem()
    {
        return $this->hasOne(InvoiceLineItem::className(), ['id' => 'invoiceLineItemId']);
    }

    public function getLineItemEnrolment()
    {
        return $this->hasOne(InvoiceItemEnrolment::className(), ['invoiceLineItemId' => 'invoiceLineItemId']);
    }

    public function getLesson()
    {
        return $this->hasOne(Lesson::className(), ['id' => 'lessonId']);
    }
}
