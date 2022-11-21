<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "location_debt".
 *
 * @property string $id
 * @property integer $locationId
 * @property integer $type
 * @property double $value
 * @property string $since
 */
class LocationDebt extends \yii\db\ActiveRecord
{
    const TYPE_ROYALTY = 1;
    const TYPE_ADVERTISEMENT = 2;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'location_debt';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['locationId', 'type', 'value'], 'required'],
            [['locationId', 'type'], 'integer'],
            [['value'], 'number'],
            [['since'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'locationId' => 'Location ID',
            'type' => 'Type',
            'value' => 'Value',
            'since' => 'Since',
        ];
    }
}
