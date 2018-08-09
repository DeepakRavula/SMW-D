<?php

namespace common\models;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

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
            [['date', 'isDeleted'], 'safe'],
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
}
