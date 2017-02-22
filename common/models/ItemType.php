<?php

namespace common\models;

/**
 * This is the model class for table "item_type".
 *
 * @property string $id
 * @property string $name
 */
class ItemType extends \yii\db\ActiveRecord
{
    const TYPE_PRIVATE_LESSON = 1;
    const TYPE_GROUP_LESSON = 2;
    const TYPE_MISC = 3;
    const TYPE_OPENING_BALANCE = 4;
    const TYPE_LESSON_CREDIT = 5;
    const TYPE_PAYMENT_CYCLE_PRIVATE_LESSON = 6;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'item_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 20],
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
        ];
    }

    public function getItemCode()
    {
        $code = null;
        switch ($this->id) {
            case self::TYPE_PRIVATE_LESSON:
                $code = 'PRIVATE LESSON';
            break;
            case self::TYPE_GROUP_LESSON:
                $code = 'GROUP LESSON';
            break;
            case self::TYPE_MISC:
                $code = 'MISC';
            break;
            case self::TYPE_OPENING_BALANCE:
                $code = 'Opening Balance';
            break;
            case self::TYPE_LESSON_CREDIT:
                $code = 'Lesson Credit';
            break;
            case self::TYPE_PAYMENT_CYCLE_PRIVATE_LESSON:
                $code = 'PRIVATE LESSON';
            break;
        }

        return $code;
    }
}
