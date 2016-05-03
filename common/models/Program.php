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
	const STATUS_ACTIVE = 1;
	const STATUS_INACTIVE = 0;
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
            [['name'], 'string', 'max' => 11],
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
        ];
    }

}
