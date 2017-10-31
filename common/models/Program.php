<?php

namespace common\models;

use Yii;
use common\models\query\ProgramQuery;

/**
 * This is the model class for table "program".
 *
 * @property int $id
 * @property string $name
 * @property int $rate
 * @property int $status
 */
class Program extends \yii\db\ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    const TYPE_PRIVATE_PROGRAM = 1;
    const TYPE_GROUP_PROGRAM = 2;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%program}}';
    }

    /**
     * @return UserQuery
     */
    public static function find()
    {
        return new ProgramQuery(get_called_class(),parent::find());
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'rate'], 'required'],
            [['name'], 'string', 'min' => 3, 'max' => 255],
            [['rate'], 'number'],
            [['status'], 'integer'],
            [['type'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'rate' => 'Rate',
            'status' => 'Status',
            'type' => 'Type',
			'showAllPrograms' => 'Show All'
        ];
    }
    public function behaviors()
    {
        return [
            
        ];
    }
   
    public function beforeSave($insert)
    {
        if ($insert) {
            $this->status = self::STATUS_ACTIVE;
        }

        return parent::beforeSave($insert);
    }

    /**
     * Returns program statuses list.
     *
     * @return array|mixed
     */
    public static function statuses()
    {
        return [
            self::STATUS_INACTIVE => Yii::t('common', 'In Active'),
            self::STATUS_ACTIVE => Yii::t('common', 'Active'),
        ];
    }
	public static function types()
    {
        return [
            self::TYPE_PRIVATE_PROGRAM => Yii::t('common', 'Private'),
            self::TYPE_GROUP_PROGRAM => Yii::t('common', 'Group'),
        ];
    }

    public function getQualification()
    {
        return $this->hasOne(Qualification::className(), ['program_id' => 'id']);
    }

    public function getCourse()
    {
        return $this->hasMany(Course::className(), ['programId' => 'id']);
    }

	public function isPrivate()
	{
		return (int) $this->type === self::TYPE_PRIVATE_PROGRAM;
	}

	public function isGroup()
	{
		return (int) $this->type === self::TYPE_GROUP_PROGRAM;
	}
	public function deletable()
	{
		$course = Course::find()
			->innerJoinWith(['program' =>function($query) {
				$query->andWhere(['programId' => $this->id]);
			}]) 
			->exists();
		return empty($course);
	}
}
