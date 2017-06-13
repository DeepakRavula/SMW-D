<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "item_category".
 *
 * @property string $id
 * @property string $name
 */
class ItemCategory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'item_category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 150],
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

    /**
     * @inheritdoc
     * @return \common\models\query\ItemCategoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\ItemCategoryQuery(get_called_class());
    }
}
