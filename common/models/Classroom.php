<?php

namespace common\models;
use Yii;

/**
 * This is the model class for table "class_room".
 *
 * @property string $id
 * @property string $name
 */
class Classroom extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'classroom';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name','description'], 'required'],
            [['name'], 'trim'],
            [['locationId'], 'integer'],
            [['name'], 'string', 'max' => 30],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Shortname',
            'description' => 'Longname',
        ];
    }

    public function getLessons()
    {
        return $this->hasMany(Lesson::className(), ['classroomId' => 'id']);
    }
}
