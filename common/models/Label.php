<?php

namespace common\models;

use common\models\query\LabelQuery;
use Yii;

/**
 * This is the model class for table "label".
 *
 * @property integer $id
 * @property string $name
 * @property integer $userAdded
 */
class Label extends \yii\db\ActiveRecord
{
    const LABEL_HOME = 1;
    const LABEL_WORK = 2;
    const LABEL_OTHER = 3;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'label';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'userAdded'], 'required'],
            [['name'], 'trim'],
            [['userAdded'], 'integer'],
            [['name'], 'string', 'max' => 255],
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
            'userAdded' => 'User Added',
        ];
    }
    
    public static function labels()
    {
        return [
            self::LABEL_HOME => Yii::t('common', 'Home'),
            self::LABEL_WORK => Yii::t('common', 'Work'),
            self::LABEL_OTHER => Yii::t('common', 'Other'),
        ];
    }
    
    public static function find()
    {
        return new LabelQuery(get_called_class());
    }
}
