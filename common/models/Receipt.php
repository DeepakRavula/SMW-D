<?php

namespace common\models;
use common\models\PaymentReceipt;
use Yii;

/**
 * This is the model class for table "receipt".
 *
 * @property int $id
 * @property int $userId
 * @property int $locationId
 * @property string $date
 * @property int $receiptNumber
 */
class Receipt extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    const TYPE_LESSON = 1;
    const TYPE_INVOICE = 2;
    const TYPE_CREDIT_INVOICE   =   3;
    const TYPE_CREDIT_PAYMENT   =   4;

    public $lessonIds;
    public $invoiceIds;
    public $creditIds;
    public static function tableName()
    {
        return 'receipt';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userId', 'locationId', 'receiptNumber'], 'required'],
            [['userId', 'locationId', 'receiptNumber'], 'integer'],
            [['date'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'userId' => 'User ID',
            'locationId' => 'Location ID',
            'date' => 'Date',
            'receiptNumber' => 'Receipt Number',
        ];
    }
        public function beforeSave($insert)
    {
        $lastReceipt   = $this->lastReceipt();
        $receiptNumber = 1;
        if (!empty($lastReceipt)) {
            $receiptNumber = $lastReceipt->receiptNumber + 1;
        }
        else{
            $receiptNumber=1;
        }
        $this->receiptNumber = $receiptNumber;
        return parent::beforeSave($insert);
    }
    public function afterSave($insert, $changedAttributes)
    {
        return parent::afterSave($insert, $changedAttributes);
    }
     public function lastReceipt()
    {
        return $query = Receipt::find()->alias('i')
                    ->andWhere(['i.locationId' => $this->locationId])
                    ->orderBy(['i.id' => SORT_DESC])
                    ->one();
    }
}
