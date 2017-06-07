<?php

namespace common\models;

/**
 * This is the model class for table "private_lesson".
 *
 * @property string $id
 * @property string $lessonSplitId
 * @property string $extendedLessonId
 * @property int $mergedOn
 */
class LessonSplitUsage extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lesson_split_usage';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lessonSplitId', 'extendedLessonId'], 'required'],
            [['lessonSplitId', 'extendedLessonId'], 'integer']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'lessonSplitId' => 'Lesson Split ID',
            'extendedLessonId' => 'Extended Lesson ID',
            'mergedOn' => 'Merged On',
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @return \common\models\query\PrivateLessonQuery the active query used by this AR class
     */
    public static function find()
    {
        return new \common\models\query\LessonSplitUsageQuery(get_called_class());
    }
    
    public function getLesson()
    {
        return $this->hasOne(Lesson::className(), ['id' => 'lessonId'])
            ->via('lessonSplit');
    }

    public function getLessonSplit()
    {
        return $this->hasOne(LessonSplit::className(), ['id' => 'lessonSplitId']);
    }

    public function getExtendedLesson()
    {
        return $this->hasOne(Lesson::className(), ['id' => 'extendedLessonId']);
    }

    public function getLessonSplitId()
    {
        $model = self::findOne(['lessonSplitId' => $this->lessonSplitId]);
        if (!empty($model)) {
            foreach ($this->lesson->lessonSplits as $split) {
                if ($split->id !== $this->lessonSplitId) {
                    return $split->id;
                }
            }
        }
        return $this->lessonSplitId;
    }

    public function afterSave($insert,$changedAttributes)
    {
        if ($insert) {
            if ($this->lesson->hasInvoice()) {
                $this->lesson->invoice->addLessonCreditUsage($this->lessonSplitId);
            } else {
                $this->lesson->proFormaInvoice->addLessonCreditUsage($this->lessonSplitId);
            }
            if ($this->extendedLesson->hasInvoice()) {
                $this->extendedLesson->invoice->addLessonCreditApplied($this->lessonSplitId);
            }
        }

        return parent::afterSave($insert, $changedAttributes);
    }
}
