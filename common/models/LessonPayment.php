<?php

namespace common\models;

use yii2tech\ar\softdelete\SoftDeleteBehavior;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use asinfotrack\yii2\audittrail\behaviors\AuditTrailBehavior;


/**
 * This is the model class for table "city".
 *
 * @property int $id
 * @property string $name
 * @property int $province_id
 */
class LessonPayment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */

    public $receiptId;

    const CONSOLE_USER_ID  = 727;

    public static function tableName()
    {
        return '{{%lesson_payment}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lessonId', 'paymentId', 'enrolmentId'], 'integer'],
            [['isDeleted','receiptId', 'date', 'createdByUserId', 
            'updatedByUserId', 'updatedOn', 'createdOn'], 'safe'],
        //    [['amount'], 'validateIsOwing']
        ];
    }

    public function behaviors()
    {
        return [
            'softDeleteBehavior' => [
                'class' => SoftDeleteBehavior::className(),
                'softDeleteAttributeValues' => [
                    'isDeleted' => true,
                ],
                'replaceRegularDelete' => true
            ],
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'createdOn',
                'updatedAtAttribute' => 'updatedOn',
                'value' => (new \DateTime())->format('Y-m-d H:i:s'),
            ],
            [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'createdByUserId',
                'updatedByAttribute' => 'updatedByUserId'
            ],
            'audittrail' => [
                'class' => AuditTrailBehavior::className(), 
                'consoleUserId' => self::CONSOLE_USER_ID, 
                'attributeOutput' => [
                    'last_checked' => 'datetime',
                ],
            ],
        ];
    }

    public function validateIsOwing($attributes)
    {
        if ($this->isNewRecord && !$this->lesson->isOwing($this->lesson->enrolment->id)) {
            $this->addError($attributes, "Lesson is already Paid");
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function find()
    {
        return new \common\models\query\LessonPaymentQuery(get_called_class());
    }

    public function getLesson()
    {
        return $this->hasOne(Lesson::className(), ['id' => 'lessonId']);
    }

    public function getGroupLesson()
    {
        return $this->hasOne(GroupLesson::className(), ['lessonId' => 'lessonId'])
            ->onCondition(['group_lesson.enrolmentId' => $this->enrolmentId]);
    }

    public function getEnrolment()
    {
        return $this->hasOne(Enrolment::className(), ['id' => 'enrolmentId']);
    }

    public function getPayment()
    {
        return $this->hasOne(Payment::className(), ['id' => 'paymentId']);
    }

    public function getCreditUsage()
    {
        return $this->hasOne(CreditUsage::className(), ['credit_payment_id' => 'paymentId']);
    }

    public function getCredit()
    {
        $payment = Payment::findOne(['id' => $this->paymentId]);
        
        return $payment->amount;
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->isDeleted = false;
            if (!$this->date) {
                $this->date = (new \DateTime($this->date))->format('Y-m-d H:i:s');
            }
        }
        if (round($this->amount, 2) == 0.00) {
            $this->isDeleted = true;
        }
        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if (!$insert) {
            if ($this->payment->isAutoPayments()) {
                if ($this->payment->isCreditApplied()) {
                    if ($this->payment->creditUsage->debitUsagePayment) {
                        $this->payment->creditUsage->debitUsagePayment->updateAttributes(['amount' => - ($this->amount)]);
                        if ($this->payment->creditUsage->debitUsagePayment->invoicePayment) {
                            $this->payment->creditUsage->debitUsagePayment->invoicePayment->updateAttributes(['amount' => - ($this->amount)]);
                            $this->payment->creditUsage->debitUsagePayment->invoice->save();
                        } else if ($this->payment->creditUsage->debitUsagePayment->lessonPayment) {
                            $this->payment->creditUsage->debitUsagePayment->lessonPayment->updateAttributes(['amount' => - ($this->amount)]);
                        }
                    }
                } else {
                    if ($this->payment->debitUsage->creditUsagePayment) {
                        $this->payment->debitUsage->creditUsagePayment->updateAttributes(['amount' => $this->amount]);
                        if ($this->payment->creditUsage->debitUsagePayment->invoicePayment) {
                            $this->payment->creditUsage->debitUsagePayment->invoicePayment->updateAttributes(['amount' => $this->amount]);
                            $this->payment->creditUsage->debitUsagePayment->invoice->save();
                        } else if ($this->payment->creditUsage->debitUsagePayment->lessonPayment) {
                            $this->payment->creditUsage->debitUsagePayment->lessonPayment->updateAttributes(['amount' => $this->amount]);
                        }
                    }
                }
                $this->payment->updateAttributes(['amount' => $this->amount]);
            }
        }
        $this->payment->save();
        if ($this->lesson->privateLesson) {
            $this->lesson->privateLesson->save();
        }
        if ($this->groupLesson) {
            $this->groupLesson->save();
        } 
        $this->lesson->customer->updateCustomerBalance();
        return parent::afterSave($insert, $changedAttributes);
    }

    public function afterSoftDelete()
    {
        if ($this->payment->isAutoPayments() && !$this->payment->isDeleted) {
            $this->payment->delete();
        }
        $this->payment->save();
        if ($this->lesson->privateLesson) {
            $this->lesson->privateLesson->save();
        }
        if ($this->groupLesson) {
            $this->groupLesson->save();
        } 
        return true;
    }
}
