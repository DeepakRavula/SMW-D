<?php

namespace common\models;
use yii2tech\ar\softdelete\SoftDeleteBehavior;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;

/**
 * This is the model class for table "blog".
 *
 * @property string $id
 * @property string $user_id
 * @property string $title
 * @property string $content
 * @property string $date
 */
class Blog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'blog';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'title', 'content'], 'required'],
            [['user_id'], 'integer'],
            [['title', 'content'], 'string'],
            [['title', 'content'], 'trim'],
            [['date', 'isDeleted', 'createdByUserId', 'updatedByUserId', 'updatedOn', 'createdOn'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User Name',
            'title' => 'Title',
            'content' => 'Content',
            'date' => 'Date',
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
            ],
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'createdOn',
                'updatedAtAttribute' => 'updatedOn',
                'value' => (new \DateTime())->format('Y-m-d H:i:s'),
            ],
            [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'createdByUserId',
                'updatedByAttribute' => 'updatedByUserId'
            ],
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->isDeleted = false;
        }
        return parent::beforeSave($insert);
    }

    public static function find()
    {
        return new \common\models\query\BlogQuery(get_called_class());
    }
}
