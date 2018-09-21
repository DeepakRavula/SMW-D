<?php

namespace common\models;

use yii2tech\ar\softdelete\SoftDeleteBehavior;
use common\models\log\InvoiceLog;
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
            [['startDate', 'endDate', 'validFrom', 'validThru', 'isDeleted'], 'safe'],
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
        return $this->hasMany(PaymentCycleLesson::className(), ['paymentCycleId' => 'id'])
            ->onCondition(['payment_cycle_lesson.isDeleted' => false]);
    }

    public function getPaymentCycleLesson()
    {
        return $this->hasOne(PaymentCycleLesson::className(), ['paymentCycleId' => 'id'])
            ->onCondition(['payment_cycle_lesson.isDeleted' => false]);
    }
    
    public function getLessons()
    {
        return $this->hasMany(Lesson::className(), ['id' => 'lessonId'])
                ->via('paymentCycleLessons')
                ->onCondition(['lesson.isDeleted' => false]);
    }

    public function getLesson()
    {
        return $this->hasOne(Lesson::className(), ['id' => 'lessonId'])
                ->via('paymentCycleLessons')
                ->onCondition(['lesson.isDeleted' => false]);
    }

    public function beforeSoftDelete()
    {
        if ($this->proFormaInvoice && !$this->proFormaInvoice->hasPayments()) {
            $this->proFormaInvoice->trigger(Invoice::EVENT_DELETE);
            $this->proFormaInvoice->delete();
        }
        foreach ($this->paymentCycleLessons as $payemntCycleLesson) {
            $payemntCycleLesson->delete();
        }
        return true;
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
                ->onCondition(['invoice_line_item.item_type_id' => ItemType::TYPE_PAYMENT_CYCLE_PRIVATE_LESSON,
                    'invoice_line_item.isDeleted' => false]);
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

    public function hasPartialyPaidLesson()
    {
        $status = false;
        $fromDate = new \DateTime($this->startDate);
        $toDate = new \DateTime($this->endDate);
        $lessons = Lesson::find()
            ->notDeleted()
            ->isConfirmed()
            ->notCanceled()
            ->course($this->enrolment->courseId)
            ->between($fromDate, $toDate)
            ->all();
        foreach ($lessons as $lesson) {
            if (($lesson->hasPayment() && round($lesson->getOwingAmount($this->enrolment->id), 2) != 0.00)) {
                $status = true;
                break;
            }
        }
        return $status;
    }

    public function hasLessonPayment()
    {
        $status = false;
        $fromDate = new \DateTime($this->startDate);
        $toDate = new \DateTime($this->endDate);
        $lessons = Lesson::find()
            ->notDeleted()
            ->isConfirmed()
            ->notCanceled()
            ->course($this->enrolment->courseId)
            ->between($fromDate, $toDate)
            ->all();
        foreach ($lessons as $lesson) {
            if ($lesson->hasPayment()) {
                $status = true;
                break;
            }
        }
        return $status;
    }

    public function isFullyPaid()
    {
        $status = true;
        $fromDate = new \DateTime($this->startDate);
        $toDate = new \DateTime($this->endDate);
        $lessons = Lesson::find()
            ->notDeleted()
            ->isConfirmed()
            ->notCanceled()
            ->course($this->enrolment->courseId)
            ->between($fromDate, $toDate)
            ->all();
        foreach ($lessons as $lesson) {
            if (round($lesson->getOwingAmount($this->enrolment->id), 2) != 0.00) {
                $status = false;
                break;
            }
        }
        return $status;
    }

    public function hasInvoicedLesson()
    {
        $status = false;
        $fromDate = new \DateTime($this->startDate);
        $toDate = new \DateTime($this->endDate);
        $lessons = Lesson::find()
            ->notDeleted()
            ->isConfirmed()
            ->notCanceled()
            ->course($this->enrolment->courseId)
            ->between($fromDate, $toDate)
            ->all();
        foreach ($lessons as $lesson) {
            if ($lesson->invoice) {
                $status = true;
                break;
            }
        }
        return $status;
    }

    public function createPaymentCycleLesson()
    {
        $startDate  = new \DateTime($this->startDate);
        $endDate    = new \DateTime($this->endDate);
        $lessons = Lesson::find()
                    ->isConfirmed()
                    ->notDeleted()
                    ->notCanceled()
                    ->andWhere(['courseId' => $this->enrolment->course->id])
                    ->between($startDate, $endDate)
                    ->all();
        foreach ($lessons as $lesson) {
            $paymentCycleLesson = new PaymentCycleLesson();
            $paymentCycleLesson->paymentCycleId = $this->id;
            $paymentCycleLesson->lessonId = $lesson->id;
            $paymentCycleLesson->save();
        }
        return true;
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->isDeleted = false;
        }
        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            $this->createPaymentCycleLesson();
        }
        return parent::afterSave($insert, $changedAttributes);
    }

    public function isPastPaymentCycle()
    {
        return new \DateTime($this->endDate) < new \DateTime();
    }

    public function isCurrentPaymentCycle()
    {
        return $this->id === $this->currentPaymentCycle->id;
    }

    public function hasUpcommimgPaymentCycle()
    {
        $currentPaymentCycle = self::find()
            ->andWhere(['enrolmentId' => $this->enrolmentId])
            ->notDeleted()
            ->andWhere(['AND',
                ['<=', 'startDate', (new \DateTime())->format('Y-m-d')],
                ['>=', 'endDate', (new \DateTime())->format('Y-m-d')]
            ])
            ->one();
        return !empty($currentPaymentCycle);
    }

    public function isFirstPaymentCycle()
    {
        if ($this->hasFirstPaymentCycle()) {
            return $this->id === $this->firstPaymentCycle->id;
        }
        return false;
    }

    public function hasFirstPaymentCycle()
    {
        $firstPaymentCycle = self::find()
            ->andWhere(['enrolmentId' => $this->enrolmentId])
            ->notDeleted()
            ->orderBy(['startDate' => SORT_ASC])
            ->one();
        return !empty($firstPaymentCycle);
    }

    public function getFirstPaymentCycle()
    {
        return self::find()
            ->andWhere(['enrolmentId' => $this->enrolmentId])
            ->notDeleted()
            ->orderBy(['startDate' => SORT_ASC])
            ->one();
    }

    public function getLastProformaInvoicedPaymentCycle()
    {
        return self::find()
            ->joinWith(['paymentCycleLessons' => function ($query) {
                $query->joinWith(['invoiceItemPaymentCycleLessons' => function ($query) {
                    $query->joinWith(['invoiceLineItem' => function ($query) {
                        $query->joinWith(['invoice' => function ($query) {
                            $query->andWhere(['invoice.isDeleted' => false])
                                ->andWhere(['NOT', ['invoice.id' => null]]);
                        }]);
                        $query->andWhere(['invoice_line_item.isDeleted' => false])
                            ->andWhere(['NOT', ['invoice_line_item.id' => null]]);
                    }])
                    ->andWhere(['NOT', ['invoice_item_payment_cycle_lesson.id' => null]]);
                }])
                ->andWhere(['NOT', ['payment_cycle_lesson.id' => null]]);
            }])
            ->andWhere(['enrolmentId' => $this->enrolmentId])
            ->notDeleted()
            ->orderBy(['startDate' => SORT_DESC])
            ->one();
    }

    public function isNextPaymentCycleUninvoiced()
    {
        return $this->lastProformaInvoicedPaymentCycle ? $this->id === 
            $this->lastProformaInvoicedPaymentCycle->nextPaymentCycleUninvoiced->id : false;
    }

    public function getCurrentPaymentCycle()
    {
        $paymentCycle = self::find()
            ->andWhere(['enrolmentId' => $this->enrolmentId])
            ->notDeleted()
            ->andWhere(['AND',
                ['<=', 'startDate', (new \DateTime())->format('Y-m-d')],
                ['>=', 'endDate', (new \DateTime())->format('Y-m-d')]
            ])
            ->one();
        if (empty($paymentCycle)) {
            $paymentCycle = self::find()
                ->andWhere(['enrolmentId' => $this->enrolmentId])
                ->notDeleted()
                ->andWhere(['>', 'startDate', (new \DateTime())->format('Y-m-d')])
                ->orderBy(['startDate' => SORT_ASC])
                ->one();
        }
        return $paymentCycle;
    }

    public function hasCurrentPaymentCycle()
    {
        $paymentCycle = self::find()
            ->andWhere(['enrolmentId' => $this->enrolmentId])
            ->notDeleted()
            ->andWhere(['AND',
                ['<=', 'startDate', (new \DateTime())->format('Y-m-d')],
                ['>=', 'endDate', (new \DateTime())->format('Y-m-d')]
            ])
            ->one();
        if (empty($paymentCycle)) {
            $paymentCycle = self::find()
                ->andWhere(['enrolmentId' => $this->enrolmentId])
                ->notDeleted()
                ->andWhere(['>', 'startDate', (new \DateTime())->format('Y-m-d')])
                ->orderBy(['startDate' => SORT_ASC])
                ->one();
        }
        return !empty($paymentCycle);
    }

    public function getSecondPaymentCycle()
    {
        return self::find()
            ->andWhere(['enrolmentId' => $this->enrolmentId])
            ->andWhere(['NOT', ['id' => $this->firstPaymentCycle->id]])
            ->notDeleted()
            ->orderBy(['startDate' => SORT_ASC])
            ->one();
    }

    public function hasSecondPaymentCycle()
    {
        if ($this->hasFirstPaymentCycle()) {
            $secondPaymentCycle = self::find()
                ->andWhere(['enrolmentId' => $this->enrolmentId])
                ->andWhere(['NOT', ['id' => $this->firstPaymentCycle->id]])
                ->notDeleted()
                ->orderBy(['startDate' => SORT_ASC])
                ->one();
            return !empty($secondPaymentCycle);
        }
        return false;
    }

    public function isSecondPaymentCycle()
    {
        if ($this->hasFirstPaymentCycle()) {
            if ($this->hasFirstPaymentCycle()) {
                return $this->id === $this->secondPaymentCycle->id;
            }
        }
        return false;
    }

    public function hasNextPaymentCycle()
    {
        if ($this->hasCurrentPaymentCycle()) {
            $nextPaymentCycle = self::find()
                ->andWhere(['enrolmentId' => $this->enrolmentId])
                ->andWhere(['>', 'startDate', $this->currentPaymentCycle->endDate])
                ->notDeleted()
                ->orderBy(['startDate' => SORT_ASC])
                ->one();
            return !empty($nextPaymentCycle);
        }
        return false;
    }

    public function getNextPaymentCycle()
    {
        return self::find()
                ->andWhere(['enrolmentId' => $this->enrolmentId])
                ->andWhere(['>', 'startDate', $this->currentPaymentCycle->endDate])
                ->notDeleted()
                ->orderBy(['startDate' => SORT_ASC])
                ->one();
    }

    public function getNextPaymentCycleUninvoiced()
    {
        return self::find()
                ->andWhere(['enrolmentId' => $this->enrolmentId])
                ->andWhere(['>', 'startDate', $this->endDate])
                ->notDeleted()
                ->orderBy(['startDate' => SORT_ASC])
                ->one();
    }

    public function isNextPaymentCycle()
    {
        if ($this->hasCurrentPaymentCycle()) {
            if ($this->hasNextPaymentCycle()) {
                return $this->id === $this->nextPaymentCycle->id;
            }
        }
        return false;
    }

    public function canRaiseProformaInvoice()
    {
        return $this->isPastPaymentCycle() || $this->isCurrentPaymentCycle() || $this->isFirstPaymentCycle() ||
            $this->isNextPaymentCycle() || $this->isSecondPaymentCycle() || $this->isNextPaymentCycleUninvoiced();
    }

    public function validateCanRaisePFI($attribute)
    {
        if (!$this->canRaiseProformaInvoice()) {
            $this->addError(
                $attribute,
                'ProForma-Invoice can be generated only for current and next payment cycle only.'
            );
        }
    }
    
    public function hasLessons()
    {
        return $this->lessons;
    }
}
