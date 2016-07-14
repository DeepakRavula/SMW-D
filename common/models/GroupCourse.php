<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "group_course".
 *
 * @property string $id
 * @property string $title
 * @property integer $rate
 * @property string $length
 */
class GroupCourse extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'group_course';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'rate', 'length'], 'required'],
            [['rate'], 'integer'],
            [['length'], 'safe'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'rate' => 'Rate',
            'length' => 'Length',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\GroupCourseQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\GroupCourseQuery(get_called_class());
    }
}
