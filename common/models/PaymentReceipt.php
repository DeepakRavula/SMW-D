<?php

namespace common\models;

use common\models\Lesson;
use common\models\Payment;
use common\models\Invoice;
use commmon\models\Receipt;
use common\models\PaymentReceipt;
use Yii;

/**
 * This is the model class for table "payment_receipt".
 *
 * @property int $id
 * @property int $receiptId
 * @property int $paymentId
 * @property int $objectType
 * @property int $objectId
 * @property int $amount
 */
class PaymentReceipt extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'payment_receipt';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['receiptId', 'paymentId', 'objectType', 'objectId', 'amount'], 'required'],
            [['receiptId', 'paymentId', 'objectType', 'objectId'], 'integer'],
	        [['amount'], 'double'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'receiptId' => 'Receipt ID',
            'paymentId' => 'Payment ID',
            'objectType' => 'Object Type',
            'objectId' => 'Object ID',
            'amount' => 'Amount',
        ];
    }
    public function getLesson()
    {
        return $this->hasOne(Lesson::className(), ['id' => 'objectId'])
            ->andWhere(['payment_receipt.objectType' => Receipt::TYPE_LESSON]);
    }

    public function getPayment()
    {
        return $this->hasOne(Payment::className(), ['id' => 'paymentId']);
    }
    public function getInvoice()
    {
        return $this->hasOne(Invoice::className(), ['id' => 'lessonId'])
        ->andWhere(['payment_receipt.objectType' => Receipt::TYPE_INVOICE]);
    }

    public function getReceipt()
    {
        return $this->hasOne(Receipt::className(), ['id' => 'receiptId']);
    }
}
