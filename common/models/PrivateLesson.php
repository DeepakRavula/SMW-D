<?php

namespace common\models;

/**
 * This is the model class for table "private_lesson".
 *
 * @property string $id
 * @property string $lessonId
 * @property string $expiryDate
 * @property int $isElgible

 */
class PrivateLesson extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    const ONLINE_CLASS = 1;
    const IN_CLASS = 0;


    public $bulkRescheduleDate;

    public static function tableName()
    {
        return 'private_lesson';
    }

    public $lessonIds;
    public $teacherBulkRescheduleSourceDate;
    public $teacherBulkRescheduleDestinationDate;
    public $selectedTeacherId;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lessonId'], 'required'],
            [['lessonId'], 'integer'],
            [['expiryDate', 'total', 'balance', 'lessonIds', 'bulkRescheduleDate', 'teacherBulkRescheduleSourceDate', 'teacherBulkRescheduleDestinationDate', 'selectedTeacherId'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'lessonId' => 'Lesson ID',
            'expiryDate' => 'Expiry Date',
            'teacherBulkRescheduleSourceDate' => 'Source Date',
            'teacherBulkRescheduleDestinationDate' => 'Destination Date'
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @return \common\models\query\PrivateLessonQuery the active query used by this AR class
     */
    public static function find()
    {
        return new \common\models\query\PrivateLessonQuery(get_called_class());
    }

    public function isExpired()
    {
        return (new \DateTime($this->expiryDate))->format('Y-m-d H:i:s') <
                (new \DateTime())->format('Y-m-d H:i:s');
    }
    
    public function getLesson()
    {
        return $this->hasOne(Lesson::className(), ['id' => 'lessonId']);
    }
   

    public function split()
    {
        $model = $this->lesson;
        $enrolment = $model->enrolment;
        $lessonDurationSec = $model->durationSec;
        $splitCount = $lessonDurationSec / Lesson::DEFAULT_EXPLODE_DURATION_SEC;
        $lastLessonPrice = ($model->programRate - (round($model->programRate / ($splitCount), 2) * ($splitCount - 1))) * $splitCount;
        $pricePerLesson = round($model->programRate / $splitCount, 2) * $splitCount; 
        
        for ($i = 0; $i < $splitCount; $i++) {
            $lesson = clone $model;
            $lesson->isNewRecord = true;
            $lesson->id = null;
            $lesson->duration = Lesson::DEFAULT_MERGE_DURATION;
            $lesson->status = Lesson::STATUS_UNSCHEDULED;
            if ($i == $splitCount - 1) {
                $lesson->programRate =  $lastLessonPrice;
            } else {
                $lesson->programRate = $pricePerLesson;
            }
            $duration = gmdate('H:i:s', Lesson::DEFAULT_EXPLODE_DURATION_SEC * ($i));
            $lessonDuration = new \DateTime($duration);
            $date = new \DateTime($model->date);
            $date->add(new \DateInterval('PT' . $lessonDuration->format('H') . 'H' . $lessonDuration->format('i') . 'M'));
            $lesson->date = $date->format('Y-m-d H:i:s');
            $lesson->isExploded = true;
            $lesson->save();
            $reschedule = $model->rescheduleTo($lesson, null);
            if ($lesson->hasMultiEnrolmentDiscount()) {
                $lesson->multiEnrolmentDiscount->updateAttributes(['value' => $lesson->multiEnrolmentDiscount->value / $splitCount]);
            }
            if ($lesson->hasLineItemDiscount()) {
                if (!$lesson->lineItemDiscount->valueType) {
                    $lesson->lineItemDiscount->updateAttributes(['value' => $lesson->lineItemDiscount->value / $splitCount]);
                }
            }
            $newLesson = Lesson::findOne($lesson->id);
            $newLesson->privateLesson->save();
            if ($i == 0) {
                $firstSplitId = $lesson->id;
                $payments = $lesson->payments;
                $lessonPayments = $lesson->lessonPayments;
                foreach ($lessonPayments as $lessonPayment){
                    $lessonPayment->delete();
                }
            }
                 $firstSplitLesson = Lesson::findOne($firstSplitId);
                 $amountNeeded = $lesson->netPrice;
                    foreach ($payments as $payment){
                        $payment = Payment::findOne($payment->id);
                        if ($payment->balance > 0) { 
                                $lessonPayment = new LessonPayment();
                                $lessonPayment->lessonId    = $lesson->id;
                                $lessonPayment->paymentId   = $payment->id;
                                $lessonPayment->enrolmentId = $enrolment->id;
                            if (round($amountNeeded, 2) <= round($payment->balance, 2)) {
                                $lessonPayment->amount      = $amountNeeded;
                                $amountNeeded = $amountNeeded-$lessonPayment->amount;
                            } else {
                                $lessonPayment->amount      = $payment->balance;
                                $amountNeeded = $amountNeeded-$payment->balance;
                            }
                            $lessonPayment->save();
                            $payment->save();
                        }

                
        }
    }
        return $model->cancel();
    }
    
    public function merge($model)
    {
        $lessonSplitUsage = new LessonSplitUsage();
        $lessonSplitUsage->lessonId = $this->lessonId;
        $lessonSplitUsage->extendedLessonId = $model->id;
        $lessonSplitUsage->mergedOn = (new \DateTime())->format('Y-m-d H:i:s');
        $lessonSplitUsage->save();
        $lesson = Lesson::findOne($this->lessonId);
        $lesson->cancel();
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->total = $this->lesson->netPrice;
            $this->balance = $this->lesson->netPrice;
        } else {
            $this->total = $this->lesson->netPrice;
            $this->balance = $this->lesson->getOwingAmount($this->lesson->enrolment->id);
        }
        
        return parent::beforeSave($insert);
    }
    
    public function afterSave($insert, $changedAttributes)
    {
        if ($this->lesson->rootLesson) {
            $rootPrivateLesson = $this->lesson->rootLesson->privateLesson;
            $rootPrivateLesson->expiryDate = (new \DateTime($this->expiryDate))->format('Y-m-d H:i:s');
            $rootPrivateLesson->save();
        }

        return parent::afterSave($insert, $changedAttributes);
    }   
}
