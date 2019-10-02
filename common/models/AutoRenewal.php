<?php

namespace common\models;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;

use Yii;

/**
 * This is the model class for table "auto_renewal".
 *
 * @property int $id
 * @property int $enrolmentId
 * @property int $paymentFrequency
 * @property string $enrolmentEndDateCurrent
 * @property string $enrolmentEndDateNew
 * @property string $lastPaymentCycleStartDate
 * @property string $lastPaymentCycleEndDate
 * @property string $createdOn
 * @property int $createdByUserId
 */
class AutoRenewal extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'auto_renewal';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'createdOn',
                'updatedAtAttribute' => false,
                'value' => (new \DateTime())->format('Y-m-d H:i:s'),
            ],
            [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'createdByUserId',
                'updatedByAttribute' => false
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['enrolmentId', 'paymentFrequency'], 'required'],
            [['enrolmentId', 'paymentFrequency', 'createdByUserId'], 'integer'],
            [['enrolmentEndDateCurrent', 'enrolmentEndDateNew', 'lastPaymentCycleStartDate', 'lastPaymentCycleEndDate', 'createdOn'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'enrolmentId' => 'Enrolment ID',
            'paymentFrequency' => 'Payment Frequency',
            'enrolmentEndDateCurrent' => 'Enrolment End Date Current',
            'enrolmentEndDateNew' => 'Enrolment End Date New',
            'lastPaymentCycleStartDate' => 'Last Payment Cycle Start Date',
            'lastPaymentCycleEndDate' => 'Last Payment Cycle End Date',
            'createdOn' => 'Created On',
            'createdByUserId' => 'Created By User ID',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\AutoRenewalQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\AutoRenewalQuery(get_called_class());
    }
}
