<?php
namespace common\models\log;

use Yii;

/**
 * This is the model class for table "log_history".
 *
 * @property integer $id
 * @property integer $logId
 * @property integer $instanceId
 * @property string $instanceType
 */
class LogHistory extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'log_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
                [['logId'], 'required'],
                [['logId', 'instanceId'], 'integer'],
                [['instanceType'], 'string', 'max' => 255],
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
            'instanceId' => 'Instance ID',
            'instanceType' => 'Instance Type',
        ];
    }
}
