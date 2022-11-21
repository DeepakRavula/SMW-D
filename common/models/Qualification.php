<?php

namespace common\models;

use Yii;
use yii2tech\ar\softdelete\SoftDeleteBehavior;
use common\models\query\QualificationQuery;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;

/**
 * This is the model class for table "qualification".
 *
 * @property string $id
 * @property string $teacher_id
 * @property string $program_id
 */
class Qualification extends \yii\db\ActiveRecord
{
    public $programs;
    const TYPE_HOURLY = 1;
    const TYPE_FIXED = 2;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%qualification}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['programs'], 'required', 'when' => function ($model) {
                return $model->isNewRecord;
            }],
            [['program_id'], 'required', 'when' => function ($model) {
                return !$model->isNewRecord;
            }],
            [['teacher_id', 'program_id', 'type',], 'integer'],
            [['rate'], 'number', 'min' => 1.00, 'max' => 2000.00, 'message' => 'Invalid rate'],
            [['isDeleted', 'createdByUserId', 'updatedByUserId', 'updatedOn', 'createdOn'], 'safe']
        ];
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
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'teacher_id' => 'Teacher Name',
            'program_id' => 'Program Name ',
            'rate' => 'Rate ($/hr)'
        ];
    }
    public static function find()
    {
        return new QualificationQuery(get_called_class(), parent::find()
            ->andWhere(['qualification.isDeleted' => false]));
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeacher()
    {
        return $this->hasOne(User::className(), ['id' => 'teacher_id']);
    }

    public function getTeacherLocation()
    {
        return $this->hasOne(UserLocation::className(), ['user_id' => 'teacher_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProgram()
    {
        return $this->hasOne(Program::className(), ['id' => 'program_id']);
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->isDeleted = false;
        }
        return parent::beforeSave($insert);
    }
}
