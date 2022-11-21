<?php

namespace common\models;

use Yii;
use common\models\Location;
use common\models\query\ProformaInvoiceQuery;
use yii2tech\ar\softdelete\SoftDeleteBehavior;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "proforma_invoice".
 *
 * @property int $id
 * @property int $lesson_id
 * @property int $type
 * @property string $amount
 * @property string $date
 * @property int $status
 */
class ProformaInvoice extends \yii\db\ActiveRecord
{
    public $lessonIds;
    public $invoiceIds;
    public $lessonId;
    public $fromDate;
    public $toDate;
    public $dateRange;

    const STATUS_UNPAID = 1;
    const STATUS_PAID = 2;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'proforma_invoice';
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
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['userId', 'locationId'], 'required'],
            [['lessonIds', 'invoiceIds', 'dateRange', 'fromDate', 'toDate', 'lessonId', 
                'notes', 'status', 'dueDate', 'date', 'isMailSent'], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'invoice_number' => 'Invoice Number',
            'date' => 'Date',
            'userId' => 'Customer Name',
            'locationId' =>'location',
            'notes'  =>'Message',
            'status' => 'Status',
            'dueDate' => 'Due Date',
            'isMailSent' => 'Mail Sent',
            'isDeleted' => 'isDeleted'
            
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @return InvoiceQuery the active query used by this AR class
     */
    public static function find()
    {
        return new ProformaInvoiceQuery(get_called_class());
    }

    public function getTotal()
    {
        return $this->subTotal;
    }
    
    public function getProformaInvoiceNumber()
    {
        $proformaInvoiceNumber = str_pad($this->proforma_invoice_number, 5, 0, STR_PAD_LEFT);
            return 'PR-'.$proformaInvoiceNumber;
    }
    
    public function beforeSave($insert)
    {
        if ($insert) {
            $this->isDeleted = false;
            $lastInvoice   = $this->lastInvoice();
            if (!empty($lastInvoice)) {
                $proformaInvoiceNumber = $lastInvoice->proforma_invoice_number + 1;
            } else {
                $proformaInvoiceNumber = 1;
            }
            $this->proforma_invoice_number = $proformaInvoiceNumber;
            $this->date = (new \DateTime())->format('Y-m-d');
            $this->dueDate = (new \DateTime())->format('Y-m-d');
            $this->isDueDateAdjusted = false;
            $this->isMailSent = false;
            $this->status = self::STATUS_UNPAID;
        } else {
            $invoiceId = $this->id;
            $lesson = Lesson::find()
                ->joinWith(['proformaLessonItems' => function ($query) use ($invoiceId) {
                    $query->joinWith(['proformaLineItem' => function ($query) use ($invoiceId) {
                        $query->notDeleted()
                            ->andWhere(['proforma_line_item.proformaInvoiceId' => $invoiceId]);
                    }]);
                }])
                ->orderBy(['lesson.date' => SORT_ASC])
                ->one();
            if ($lesson && !$this->isDueDateAdjusted) {
                if (new \DateTime($lesson->date) > new \DateTime()) {
                    $this->dueDate = (new \DateTime($lesson->date))->format('Y-m-d');
                }
            }
            $this->status = round($this->total, 2) > 0.00 ? self::STATUS_UNPAID : self::STATUS_PAID;
        }
        
        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if (!$insert) {
            foreach ($this->proformaLineItems as $proformaLineItem) {
                $proformaLineItem->save();
            }
            if (round($this->total, 2) == 0.00) {
                $this->delete();
            }
        }
        return parent::afterSave($insert, $changedAttributes);
    }
    
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'userId']);
    }

    public function getStatus()
    {
        $status = null;
        switch ($this->status) {
            case self::STATUS_UNPAID:
                $status = 'Unpaid';
            break;
            case self::STATUS_PAID:
                $status = 'Paid';
            break;
        }
        return $status;
    }

    public function isCreatedByBot()
    {
        $user = User::findByRole(User::ROLE_BOT);
		$botUser = end($user);
        return $this->createdByUserId == $botUser->id;
    }

    public function lastInvoice()
    {
        return $query = ProformaInvoice::find()
                    ->andWhere(['locationId' => $this->locationId])
                    ->orderBy(['id' => SORT_DESC])
                    ->notDeleted()
                    ->one();
    }
    
    public function getLocation()
    {
        return $this->hasOne(Location::className(), ['id' => 'locationId']);
    }

    public function getTotalDiscount()
    {
        $discount = 0.0;
        $lineItems = $this->proformaLineItems;
        foreach ($lineItems as $lineItem) {
            if ($lineItem->lessonLineItem) {
                $discount += $lineItem->lesson->discount;
            }
            if ($lineItem->invoiceLineItem) {
                $discount += $lineItem->invoice->totalDiscount;
            }
           
        }
        return $discount;
    }

    public function isPaid()
    {
        return (int) $this->status === (int) self::STATUS_PAID;
    }

    public function getSubtotal()
    {
        $subtotal = 0.0;
        $lineItems = $this->proformaLineItems;
        foreach ($lineItems as $lineItem) {
            if ($lineItem->lessonLineItem) {
                if ($lineItem->lesson->isPrivate()) {
                    $enrolmentId = $lineItem->lesson->enrolment->id;
                } else {
                    $enrolment = Enrolment::find()
                        ->notDeleted()
                        ->isConfirmed()
                        ->andWhere(['courseId' => $lineItem->lesson->courseId])
                        ->customer($this->userId)
                        ->one();
                    $enrolmentId = $enrolment->id;
                }
                $subtotal += $lineItem->lesson->getOwingAmount($enrolmentId);
            }
            if ($lineItem->invoiceLineItem) {
                $subtotal += $lineItem->invoice->balance;
            }
        }
        return $subtotal;
    }

    public function getPrStatus()
    {
        return round($this->total, 2) > 0.00 ? 'Unpaid' : 'Paid';
    }
    
    public function getProformaLineItems()
    {
        return $this->hasMany(ProformaLineItem::className(), ['proformaInvoiceId' => 'id'])
            ->onCondition(['proforma_line_item.isDeleted' => false]);
    }

    public function getReminderNotes() 
    {
		$reminderNote =  ReminderNote::find()->one();
		return $reminderNote->notes; 
    }
}
