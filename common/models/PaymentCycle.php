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

    public function getEnrolment()
    {
        return $this->hasOne(Enrolment::className(), ['id' => 'enrolmentId']);
    }

    public function getProFormaInvoice()
    {
        foreach ($this->paymentCycleLessons as $paymentCycleLesson) {
            if (!empty($paymentCycleLesson->proFormaInvoice)) {
                return $paymentCycleLesson->proFormaInvoice;
            }
        }

        return null;
    }

    public function hasProFormaInvoice()
    {
        return !empty($this->proFormaInvoice);
    }

    public function beforeDelete()
    {
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
            ->andWhere(['status' => Lesson::STATUS_SCHEDULED])
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
        $invoice->date = $this->enrolment->firstPaymentCycle->startDate;
        $invoice->type = INVOICE::TYPE_PRO_FORMA_INVOICE;
		$invoice->createdUserId = Yii::$app->user->id;
		$invoice->updatedUserId = Yii::$app->user->id;
        $invoice->save();
        $startDate = \DateTime::createFromFormat('Y-m-d', $this->startDate);
        $endDate   = \DateTime::createFromFormat('Y-m-d', $this->endDate);
        $lessons = Lesson::find()
            ->location($locationId)
            ->unInvoicedProForma()
            ->andWhere(['courseId' => $this->enrolment->courseId])
            ->between($startDate, $endDate)
            ->andWhere(['lesson.status' => Lesson::STATUS_SCHEDULED])
            ->all();
        foreach ($lessons as $lesson) {
            $lesson->studentFullName = $this->enrolment->student->fullName;
            $invoice->addLineItem($lesson);
        }
        $invoice->save();
        return $invoice;
        }
    }
