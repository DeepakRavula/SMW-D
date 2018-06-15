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
        $lessonDurationSec = $model->durationSec;
        for ($i = 0; $i < $lessonDurationSec / Lesson::DEFAULT_EXPLODE_DURATION_SEC; $i++) {
            $lesson = clone $model;
            $lesson->isNewRecord = true;
            $lesson->id = null;
            $lesson->duration = Lesson::DEFAULT_MERGE_DURATION;
            $lesson->status = Lesson::STATUS_UNSCHEDULED;
            $duration = gmdate('H:i:s', Lesson::DEFAULT_EXPLODE_DURATION_SEC * ($i +1));
            $lessonDuration = new \DateTime($duration);
            $date = new \DateTime($model->date);
            $date->add(new \DateInterval('PT' . $lessonDuration->format('H') . 'H' . $lessonDuration->format('i') . 'M'));
            $lesson->date = $date->format('Y-m-d H:i:s');
            $lesson->isExploded = true;
            $lesson->save();
            $reschedule = $model->rescheduleTo($lesson);
            if ($model->hasProFormaInvoice()) {
                $lesson->addPrivateLessonLineItem($model->proFormaInvoice);
            }
        }
        if ($model->proFormaLineItem) {
            $model->proFormaLineItem->delete();
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
        if($this->lesson->rootLesson) {
            $rootPrivateLesson = $this->lesson->rootLesson->privateLesson;
            $rootPrivateLesson->expiryDate = (new \DateTime($this->expiryDate))->format('Y-m-d H:i:s');
            $rootPrivateLesson->save();
        }

        return parent::afterSave($insert, $changedAttributes);
    }   
}
