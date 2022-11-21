<?php

namespace common\models;

use Yii;
use yii2tech\ar\softdelete\SoftDeleteBehavior;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use common\models\query\GroupLessonQuery;
use asinfotrack\yii2\audittrail\behaviors\AuditTrailBehavior;

/**
 * This is the model class for table "group_lesson".
 *
 * @property int $id
 * @property int $lessonId
 * @property int $enrolmentId
 * @property string $total
 * @property string $balance
 * @property int $paidStatus
 */
class GroupLesson extends \yii\db\ActiveRecord
{
    const STATUS_OWING = 1;
    const STATUS_PAID = 2;
    const STATUS_CREDIT = 3;

    const CONSOLE_USER_ID  = 727;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'group_lesson';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['lessonId', 'enrolmentId'], 'required'],
            [['lessonId', 'enrolmentId', 'paidStatus'], 'integer'],
            [['total', 'balance'], 'number'],
            [['dueDate', 'createdOn', 'updatedOn', 'createdByUserId', 'updatedByUserId', 'isDeleted'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'lessonId' => 'Lesson ID',
            'enrolmentId' => 'Enrolment ID',
            'total' => 'Total',
            'balance' => 'Balance',
            'paidStatus' => 'Paid Status',
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

    /**
     * @inheritdoc
     * @return GroupLessonQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new GroupLessonQuery(get_called_class());
    }

    public function getLesson()
    {
        return $this->hasOne(Lesson::className(), ['id' => 'lessonId']);
    }

    public function getEnrolment()
    {
        return $this->hasOne(Enrolment::className(), ['id' => 'enrolmentId']);
    }

    public function getInvoiceItemsEnrolment()
    {
        return $this->hasMany(InvoiceItemEnrolment::className(), ['enrolmentId' => 'enrolmentId']);
    }

    public function getInvoiceItemLessons()
    {
        return $this->hasMany(InvoiceItemLesson::className(), ['lessonId' => 'lessonId']);
    }

    public function beforeSave($insert) 
    {
        $this->total = $this->lesson->getGroupNetPrice($this->enrolment);
        $this->balance = $this->total - $this->lesson->getCreditAppliedAmount($this->enrolmentId);
        $this->paidStatus = $this->getStatus();
        if ($insert) {
            $this->isDeleted = false;
        }        

        return parent::beforeSave($insert);
    }

    public function beforeSoftDelete()
    {  
        if ($this->lessonPayments) {
        foreach($this->lessonPayments as $lessonPayment){
            $lessonPayment->delete();
        }
    }
       
        return true;
    }

    public function getLessonPayments()
    {
        return $this->hasMany(LessonPayment::className(), ['lessonId' => 'lessonId'],['enrolmentId' => 'enrolmentId'])
             ->onCondition(['lesson_payment.isDeleted' => false]);
    }

    public function getOwingStatus() 
    {
        return $this->balance > 0.0 ? 'Unpaid' : 'Paid';
    }

    public function isOwing()
    {
        return $this->balance > 0.0;
    }

    public function getStatus() 
    {
        $paidStatus = 0;
        if ($this->balance == 0) {
            $paidStatus = self::STATUS_PAID;
        } else if ($this->balance > 0) {
            $paidStatus = self::STATUS_OWING;
        } else if ($this->balance < 0) {
            $paidStatus = self::STATUS_CREDIT;
        }
        
        return $paidStatus;
    }

    public function afterSave($insert, $changedAttributes)
    {
        $this->enrolment->customer->updateCustomerBalance();
        return parent::afterSave($insert, $changedAttributes);
    }
}
