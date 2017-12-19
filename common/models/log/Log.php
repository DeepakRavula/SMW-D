<?php

namespace common\models\log;

use Yii;

/**
 * This is the model class for table "log".
 *
 * @property integer $id
 * @property integer $logObjectId
 * @property integer $logActivityId
 * @property string $message
 * @property string $data
 * @property integer $locationId
 * @property string $createdOn
 * @property integer $createdUserId
 */
class Log extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['logObjectId', 'logActivityId'], 'required'],
            [['logObjectId', 'logActivityId', 'locationId', 'createdUserId'], 'integer'],
            [['message', 'data'], 'string'],
            [['createdOn'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'logObjectId' => 'Log Object ID',
            'logActivityId' => 'Log Activity ID',
            'message' => 'Message',
            'data' => 'Data',
            'locationId' => 'Location ID',
            'createdOn' => 'Created On',
            'createdUserId' => 'Created User ID',
        ];
    }
}