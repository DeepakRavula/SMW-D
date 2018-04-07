<?php

namespace common\models\log;

use Yii;
use common\models\query\LogHistoryQuery;
use common\models\Location;
use yii\helpers\Html;

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
            [['instanceType'], 'trim'],
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
    public static function find()
    {
        return new LogHistoryQuery(get_called_class(), parent::find());
    }
    public function getMessage()
    {
        $message = $this->log->message;
        $regex = '/{{([^}]*)}}/';
        $replace = preg_replace_callback($regex, function ($match) {
            $index = $match[1];
            $logLink = $this->log->getLogLink($index)->one();
            $url = $logLink->baseUrl . $logLink->path;
            $data[$index] = Html::a($index, $url);
            return isset($data[$match[0]]) ? $data[$match[0]] : $data[$match[1]] ;
        }, $message);
        return $replace;
    }
    public function getLog()
    {
        return $this->hasOne(Log::className(), ['id' => 'logId']);
    }
    public static function logsCount()
    {
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        return self::find()
		->joinWith('log')
		->andWhere(['log.locationId' => $locationId])
		->today()
		->count();
    }

}
