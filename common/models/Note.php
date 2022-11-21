<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "note".
 *
 * @property string $id
 * @property string $instanceId
 * @property integer $instanceType
 * @property string $content
 * @property string $createdUserId
 * @property string $createdOn
 * @property string $updatedOn
 */
class Note extends \yii\db\ActiveRecord
{
    const INSTANCE_TYPE_STUDENT = 1;
    const INSTANCE_TYPE_USER = 2;
    const INSTANCE_TYPE_LESSON = 3;
    const INSTANCE_TYPE_INVOICE = 4;
    const INSTANCE_TYPE_PROFORMA = 5;
    public $hasEditable;
    public $reasonToUnschedule;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'note';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['content'], 'required'],
            [['instanceId', 'instanceType', 'createdUserId'], 'integer'],
            [['content'], 'string'],
            [['content'], 'trim'],
            [['createdOn', 'updatedOn', 'hasEditable', 'reasonToUnschedule'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'instanceId' => 'Instance ID',
            'instanceType' => 'Instance Type',
            'content' => 'Content',
            'createdUserId' => 'Created User ID',
            'createdOn' => 'Created On',
            'updatedOn' => 'Updated On',
        ];
    }

    public function getCreatedUser()
    {
        return $this->hasOne(User::className(), ['id' => 'createdUserId']);
    }

    public function getInstanceTypeName()
    {
        $name = null;
        switch ($this->instanceType) {
            case self::INSTANCE_TYPE_STUDENT:
                $name = 'student';
            break;
            case self::INSTANCE_TYPE_USER:
                $name = 'user';
            break;
            case self::INSTANCE_TYPE_LESSON:
                $name = 'lesson';
            break;
            case self::INSTANCE_TYPE_INVOICE:
                $name = 'invoice';
            break;
        }
        return $name;
    }
    public function beforeSave($insert)
    {
        $currentDate = (new \DateTime())->format('Y-m-d H:i:s');
        if (! $insert) {
            $this->updatedOn = $currentDate;
        } else {
            $this->createdOn = $currentDate;
            $this->updatedOn = $currentDate;
        }
        return parent::beforeSave($insert);
    }
}
