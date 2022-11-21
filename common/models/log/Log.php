<?php
namespace common\models\log;
use common\models\User;

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
            [['message'], 'trim']
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
    public function beforeSave($insert)
    {
        if ($insert) {
            $this->createdOn = (new \DateTime())->format('Y-m-d H:i:s');
        }
        return parent::beforeSave($insert);
    }
    public function getLogLink($index)
    {
        return $this->hasMany(LogLink::className(), ['logId' => 'id'])
            ->onCondition(['log_link.index' => $index]);
    }
    public function beforeDelete() 
    {
        if ($this->logLinks) {
        foreach ($this->logLinks as $logLink) {
            $logLink->delete();
        }
    }
    if ($this->logHistory) {
        $this->logHistory->delete();
    }
        return parent::beforeDelete();
    }
    public function getLogLinks()
    {
        return $this->hasMany(LogLink::className(), ['logId' => 'id']);
    }
    public function getLogHistory()
    {
        return $this->hasOne(LogHistory::className(), ['logId' => 'id']);
    }
    public function getLogObject()
    {
        return $this->hasMany(LogObject::className(), ['id' => 'logObjectId']);
    }
    public function getCreatedUser()
    {
        return $this->hasOne(User::className(), ['id' => 'createdUserId']);
    }
}
