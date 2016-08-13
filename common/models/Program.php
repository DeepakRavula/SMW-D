<?php

namespace common\models;

use Yii;
use common\models\query\ProgramQuery;

/**
 * This is the model class for table "program".
 *
 * @property integer $id
 * @property string $name
 * @property integer $rate
 * @property integer $status
 */
class Program extends \yii\db\ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
   
	const TYPE_PRIVATE_PROGRAM = 1;
	const TYPE_GROUP_PROGRAM = 2;
    /**
     * @inheritdoc
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
        return new ProgramQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['rate', 'status'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['type'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'rate' => 'Rate',
            'status' => 'Status',
			'type' => 'Type',
        ];
    }

    public function beforeSave($insert) {
		if($this->isNewRecord) {
			$this->status = self::STATUS_ACTIVE;
		}
        
        return parent::beforeSave($insert);
    }
    
    /**
     * Returns program statuses list
     * @return array|mixed
     */
    public static function statuses()
    {
        return [
            self::STATUS_INACTIVE => Yii::t('common', 'In Active'),
            self::STATUS_ACTIVE => Yii::t('common', 'Active')
        ];
    }

	public function getQualification()
    {
        return $this->hasOne(Qualification::className(), ['program_id' => 'id']);
    }
}
