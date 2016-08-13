<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "item_type".
 *
 * @property string $id
 * @property string $name
 */
class ItemType extends \yii\db\ActiveRecord
{
	const TYPE_LESSON = 1;
	const TYPE_MISC = 2;
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'item_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 20],
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
        ];
    }
}
