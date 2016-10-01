<?php

namespace common\models;

use Yii;

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
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'blog';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'title', 'content'], 'required'],
            [['user_id'], 'integer'],
            [['title', 'content'], 'string'],
            [['date'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
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

	public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
