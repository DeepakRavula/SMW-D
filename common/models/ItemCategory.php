<?php

namespace common\models;

use yii2tech\ar\softdelete\SoftDeleteBehavior;
use Yii;

/**
 * This is the model class for table "item_category".
 *
 * @property string $id
 * @property string $name
 * @property integer $isDeleted
 */
class ItemCategory extends \yii\db\ActiveRecord
{
    const LESSON_ITEM = 'Lesson';
    const OPENING_BALANCE_ITEM = 'Opening Balance';

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
            ['isDeleted', 'safe'],
            [['name'], 'string', 'max' => 150],
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
            ]
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

    public function isOpeningBalance()
    {
        return $this->name === self::OPENING_BALANCE_ITEM;
    }

    public function isLesson()
    {
        return $this->name === self::LESSON_ITEM;
    }

    public function canUpdate()
    {
        return !$this->isLesson() && !$this->isOpeningBalance();
    }
}
