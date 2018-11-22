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
    public static function tableName()
    {
        return 'private_lesson';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lessonId'], 'required'],
            [['lessonId'], 'integer'],
            [['expiryDate'], 'safe'],
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
        for ($i = 0; $i < $splitCount; $i++) {
            $lesson = clone $model;
            $lesson->isNewRecord = true;
            $lesson->id = null;
            $lesson->duration = Lesson::DEFAULT_MERGE_DURATION;
            $lesson->status = Lesson::STATUS_UNSCHEDULED;
            $duration = gmdate('H:i:s', Lesson::DEFAULT_EXPLODE_DURATION_SEC * ($i));
            $lessonDuration = new \DateTime($duration);
            $date = new \DateTime($model->date);
            $date->add(new \DateInterval('PT' . $lessonDuration->format('H') . 'H' . $lessonDuration->format('i') . 'M'));
            $lesson->date = $date->format('Y-m-d H:i:s');
            $lesson->isExploded = true;
            $lesson->save();
            $reschedule = $model->rescheduleTo($lesson);
            if ($lesson->hasMultiEnrolmentDiscount()) {
                $lesson->multiEnrolmentDiscount->updateAttributes(['value' => $lesson->multiEnrolmentDiscount->value / $splitCount]);
            }
            if ($lesson->hasLineItemDiscount()) {
                if (!$lesson->lineItemDiscount->valueType) {
                    $lesson->lineItemDiscount->updateAttributes(['value' => $lesson->lineItemDiscount->value / $splitCount]);
                }
            }
            if ($i == 0) {
                $firstSplitId = $lesson->id;
            } else {
                $firstSplitLesson = Lesson::findOne($firstSplitId);
                if ($firstSplitLesson->hasCredit($enrolment->id)) {
                    $amountNeeded = $firstSplitLesson->netPrice;
                    $amount = 0;
                    foreach ($firstSplitLesson->lessonPayments as $firstSplitLessonPayment) {
                        $amount += $firstSplitLessonPayment->amount;
                        if (!$firstSplitLessonPayment->payment->isAutoPayments()) {
                            if ($amountNeeded < $amount) {
                                $amount -= $firstSplitLessonPayment->amount;
                                $lessonPayment = new LessonPayment();
                                $lessonPayment->lessonId    = $lesson->id;
                                $lessonPayment->paymentId   = $firstSplitLessonPayment->paymentId;
                                $amountRemin = ($amount + $firstSplitLessonPayment->amount) - $amountNeeded;
                                $lessonPayment->amount      = $lesson->netPrice >= $amountRemin ? $amountRemin : $lesson->netPrice;
                                $lessonPayment->enrolmentId = $enrolment->id;
                                $lessonPayment->save();
                                $firstSplitLessonPayment->amount -= $amountRemin;
                                $firstSplitLessonPayment->save();
                                $amount += $firstSplitLessonPayment->amount;
                            }
                        }
                    }
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
