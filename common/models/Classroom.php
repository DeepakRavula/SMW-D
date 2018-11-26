<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii2tech\ar\softdelete\SoftDeleteBehavior;
use common\models\Location;

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
            [['createdByUserId', 'updatedByUserId', 'updatedOn', 'createdOn', 'isDeleted'], 'safe'],
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

    public static function find()
    {
        return new \common\models\query\ClassroomQuery(get_called_class());
    }

    public function behaviors()
    {
        return [
            'softDeleteBehavior' => [
                'class' => SoftDeleteBehavior::className(),
                'softDeleteAttributeValues' => [
                    'isDeleted' => true,
                ],
                'replaceRegularDelete' => true
            ],
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
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $lesson = Lesson::find()
                ->notDeleted()
                ->andWhere(['classroomId' => $this->id])
                ->one();
        $teacherRoom = TeacherRoom::find()
        ->joinWith(['teacherAvailability' => function ($query) use ($locationId) {
                    $query->notDeleted()
                    ->location($locationId);
        }])
                ->andWhere(['classroomId' => $this->id])
                ->one();
        if ($lesson || $teacherRoom) {
            $this->addError($attribute, "Lessons or teacher availability associated with this classroom so it can't be deleted");
        }
    }
}
