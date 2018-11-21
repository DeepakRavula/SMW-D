<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use common\components\validators\lesson\conflict\ClassroomValidator;

/**
 * This is the model class for table "class_room".
 *
 * @property string $id
 * @property string $name
 */
class Classroom extends \yii\db\ActiveRecord
{
    const SCENARIO_DELETE_CLASSROOM = 'classroom-delete';
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
            ['name', 'validateOnDelete', 'on' => self::SCENARIO_DELETE_CLASSROOM],
            [['name'], 'trim'],
            [['locationId'], 'integer'],
            [['name'], 'string', 'max' => 30],
            [['createdByUserId', 'updatedByUserId', 'updatedOn', 'createdOn'], 'safe'],
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

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'createdOn',
                'updatedAtAttribute' => 'updatedOn',
                'value' => (new \DateTime())->format('Y-m-d H:i:s'),
            ],
            [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'createdByUserId',
                'updatedByAttribute' => 'updatedByUserId'
            ],
        ];
    }

    public function validateOnDelete($attribute) {
        $lesson = Lesson::find()
                ->notDeleted()
                ->andWhere(['classroomId' => $this->id])
                ->one();
        $teacherRoom = TeacherRoom::find()
                ->andWhere(['classroomId' => $this->id])
                ->one();
        if ($lesson || $teacherRoom) {
            $this->addError($attribute, "Lessons or teacher availability associated with this classroom so it can't be deleted");
        }
    }
}
