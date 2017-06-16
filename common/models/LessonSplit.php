<?php

namespace common\models;

/**
 * This is the model class for table "private_lesson".
 *
 * @property string $id
 * @property string $lessonId
 * @property string $unit
 */
class LessonSplit extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lesson_split';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lessonId', 'unit'], 'required'],
            [['lessonId'], 'integer']
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
            'unit' => 'Unit',
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @return \common\models\query\PrivateLessonQuery the active query used by this AR class
     */
    public static function find()
    {
        return new \common\models\query\LessonSplitQuery(get_called_class());
    }

    public function getLessonSplitUsage()
    {
        return $this->hasOne(LessonSplitUsage::className(), ['lessonSplitId' => 'id']);
    }

    public function getLesson()
    {
        return $this->hasOne(Lesson::className(), ['id' => 'lessonId']);
    }

    public function getPrivateLesson()
    {
        return $this->hasOne(PrivateLesson::className(), ['lessonId' => 'id'])
            ->via('lesson');
    }

    public function afterSave($insert,$changedAttributes)
    {
        if ($insert) {
            if ($this->lesson->hasProFormaInvoice()) {
                $this->lesson->proFormaInvoice->removeLessonItem($this->lessonId);
                $this->lesson->proFormaInvoice->addLessonSplitItem($this->id);
            }
        }

        return parent::afterSave($insert, $changedAttributes);
    }
}
