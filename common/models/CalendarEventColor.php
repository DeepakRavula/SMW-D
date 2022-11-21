<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "calendar_event_color".
 *
 * @property string $id
 * @property string $name
 * @property string $code
 * @property string $cssClass
 */
class CalendarEventColor extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calendar_event_color';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'code', 'cssClass'], 'required'],
            [['name', 'code', 'cssClass'], 'trim'],
            [['name'], 'string', 'max' => 50],
            [['code'], 'string', 'max' => 8],
            [['cssClass'], 'string', 'max' => 30],
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
            'code' => 'Code',
            'cssClass' => 'Css Class',
        ];
    }
}
