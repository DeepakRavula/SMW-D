<?php

namespace common\models;

use Yii;
use common\models\query\GroupLessonQuery;

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
            [['lessonId', 'enrolmentId', 'paidStatus'], 'required'],
            [['lessonId', 'enrolmentId', 'paidStatus'], 'integer'],
            [['total', 'balance'], 'number'],
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

    /**
     * @inheritdoc
     * @return GroupLessonQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new GroupLessonQuery(get_called_class());
    }
}
