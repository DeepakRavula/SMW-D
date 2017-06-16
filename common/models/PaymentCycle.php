<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "payment_cycle".
 *
 * @property string $id
 * @property string $enrolmentId
 * @property string $startDate
 * @property string $endDate
 * @property string $validFrom
 * @property string $validThru
 */
class PaymentCycle extends \yii\db\ActiveRecord
{
    const PFI_CREATION_THRESHOLD_ADVANCED_DAYS  = -15;

    const SCENARIO_CAN_RAISE_PFI = 'can-raise-PFI';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'payment_cycle';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['id', 'validateCanRaisePFI', 'on' => self::SCENARIO_CAN_RAISE_PFI],
            [['enrolmentId', 'startDate', 'endDate'], 'required'],
            [['enrolmentId'], 'integer'],
            [['startDate', 'endDate', 'validFrom', 'validThru'], 'safe'],
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
            'startDate' => 'Start Date',
            'endDate' => 'End Date',
            'validFrom' => 'Valid From',
            'validThru' => 'Valid Thru',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\PaymentCycleQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\PaymentCycleQuery(get_called_class());
    }

    public function getPaymentCycleLessons()
    {
        return $this->hasMany(PaymentCycleLesson::className(), ['paymentCycleId' => 'id']);
    }

    public function getInvoiceItemPaymentCycleLessons()
    {
        return $this->hasMany(InvoiceItemPaymentCycleLesson::className(), ['paymentCycleLessonId' => 'id'])
            ->via('paymentCycleLessons');
    }

    public function getInvoiceLineItems()
    {
        return $this->hasMany(InvoiceLineItem::className(), ['id' => 'invoiceLineItemId'])
            ->via('invoiceItemPaymentCycleLessons')
                ->onCondition(['invoice_line_item.item_type_id' => ItemType::TYPE_PAYMENT_CYCLE_PRIVATE_LESSON]);
    }

    public function getFirstLesson()
    {
        return $this->hasOne(Lesson::className(), ['id' => 'lessonId'])
            ->viaTable('payment_cycle_lesson', ['paymentCycleId' => 'id'])
            ->orderBy(['lesson.date' => SORT_ASC]);
    }

    public function getEnrolment()
    {
        return $this->hasOne(Enrolment::className(), ['id' => 'enrolmentId']);
    }

    public function getProFormaInvoice()
    {
        return $this->hasOne(Invoice::className(), ['id' => 'invoice_id'])
            ->via('invoiceLineItems')
                ->onCondition(['invoice.isDeleted' => false, 'invoice.type' => Invoice::TYPE_PRO_FORMA_INVOICE]);
    }

    public function hasProFormaInvoice()
    {
        return !empty($this->proFormaInvoice);
    }

    public function beforeDelete()
    {
        if ($this->proformaInvoice && !$this->proformaInvoice->isPaid()) {
            $this->proformaInvoice->trigger(Invoice::EVENT_DELETE);
            $this->proformaInvoice->delete();
        }
        PaymentCycleLesson::deleteAll(['paymentCycleId' => $this->id]);
        return parent::beforeDelete();
    }

    public function afterSave($insert, $changedAttributes)
    {
        $locationId = Yii::$app->session->get('location_id');
        $startDate  = new \DateTime($this->startDate);
        $endDate    = new \DateTime($this->endDate);
        $lessons = Lesson::find()
            ->notDeleted()
            ->location($locationId)
            ->andWhere(['courseId' => $this->enrolment->course->id])
            ->notRescheduled()
            ->andWhere(['OR', ['status' => Lesson::STATUS_SCHEDULED], ['status' => Lesson::STATUS_UNSCHEDULED]])
            ->between($startDate, $endDate)
            ->all();
        foreach ($lessons as $lesson) {
            $paymentCycleLesson                 = new PaymentCycleLesson();
            $paymentCycleLesson->paymentCycleId = $this->id;
            $paymentCycleLesson->lessonId       = $lesson->id;
            $paymentCycleLesson->save();
        }
        return parent::afterSave($insert, $changedAttributes);
    }

    public function createProFormaInvoice()
    {
        $locationId = $this->enrolment->student->customer->userLocation->location_id;
        $user = User::findOne(['id' => Yii::$app->user->id]);
        $invoice = new Invoice();
        $invoice->on(Invoice::EVENT_CREATE, [new InvoiceLog(), 'create']);
        $invoice->userName = $user->publicIdentity;
        $invoice->user_id = $this->enrolment->student->customer->id;
        $invoice->location_id = $locationId;
        $invoice->dueDate = (new \DateTime($this->firstLesson->date))->format('Y-m-d');
        $invoice->type = INVOICE::TYPE_PRO_FORMA_INVOICE;
        $invoice->createdUserId = Yii::$app->user->id;
        $invoice->updatedUserId = Yii::$app->user->id;
        $invoice->save();
        $lessons = Lesson::find()
            ->joinWith('paymentCycleLesson')
            ->andWhere(['payment_cycle_lesson.paymentCycleId' => $this->id])
            ->all();
        foreach ($lessons as $lesson) {
            if ($lesson->isExploded()) {
                foreach ($lesson->lessonSplits as $split) {
                    $invoice->addLessonSplitItem($split->id);
                }
            } else {
                $lesson->studentFullName = $this->enrolment->student->fullName;
                $invoice->addLineItem($lesson);
            }
        }
        $invoice->save();
        return $invoice;
    }

    public function isPastPaymentCycle()
    {
        return new \DateTime($this->endDate) <= new \DateTime();
    }

    public function isCurrentPaymentCycle()
    {
        if (new \DateTime($this->startDate) <= new \DateTime() &&
            new \DateTime($this->endDate) >= new \DateTime()) {
            return true;
        } else if ($this->isFirstPaymentCycle()) {
            return true;
        } else {
            return false;
        }
    }

    public function isFirstPaymentCycle()
    {
        $firstPaymentCycle = self::find()
            ->where(['enrolmentId' => $this->enrolmentId])
            ->orderBy(['startDate' => SORT_ASC])
            ->one();

        return $this->id === $firstPaymentCycle->id;
    }

    public function isNextPaymentCycle()
    {
        return $this->enrolment->nextPaymentCycle->id === $this->id;
    }

    public function canRaiseProformaInvoice()
    {
        return $this->isPastPaymentCycle() || $this->isCurrentPaymentCycle() ||
            $this->isNextPaymentCycle();
    }

    public function validateCanRaisePFI($attribute)
    {
        if (!$this->canRaiseProformaInvoice()) {
            $this->addError($attribute,
                'ProForma-Invoice can be generated only for current and next payment cycle only.');
        }
    }
}
