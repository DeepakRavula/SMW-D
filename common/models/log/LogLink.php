<?php

namespace common\models\log;

use Yii;

/**
 * This is the model class for table "log_link".
 *
 * @property integer $id
 * @property integer $logId
 * @property string $index
 * @property string $baseUrl
 * @property string $path
 */
class LogLink extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'log_link';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['logId', 'index'], 'required'],
            [['logId'], 'integer'],
            [['baseUrl', 'path'], 'string'],
            [['index'], 'string', 'max' => 255],
            [['index','baseUrl', 'path'], 'trim'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'logId' => 'Log ID',
            'index' => 'Index',
            'baseUrl' => 'Base Url',
            'path' => 'Path',
        ];
    }
    public function getLog()
    {
        return $this->hasOne(Log::className(), ['id' => 'logId']);
    }
}
