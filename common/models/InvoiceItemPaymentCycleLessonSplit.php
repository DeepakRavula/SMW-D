<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "invoice_item_payment_cycle_lesson_split".
 *
 * @property string $id
 * @property string $invoiceLineItemId
 * @property string $lessonSplitId
 */
class InvoiceItemPaymentCycleLessonSplit extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'invoice_item_payment_cycle_lesson_split';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['invoiceLineItemId', 'lessonSplitId'], 'required'],
            [['invoiceLineItemId', 'lessonSplitId'], 'integer'],
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
            'lessonSplitId' => 'Lesson Split ID',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\InvoiceItemPaymentCycleLessonSplitQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\InvoiceItemPaymentCycleLessonSplitQuery(get_called_class());
    }

    public function getLessonSplit()
    {
        return $this->hasOne(LessonSplit::className(), ['id' => 'lessonSplitId']);
    }
}
