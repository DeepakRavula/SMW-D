<?php

namespace common\models;

/**
 * This is the model class for table "email-object".
 *
 * @property string $id
 * @property string $name
 */
class TestEmail extends \yii\db\ActiveRecord
{   
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'test_email';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email'], 'required'],
            [['email'], 'trim'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email' => 'Email',
        ];
    }
    public function testEmail()
   {
       if (YII_ENV_DEV) {
           return $this->email;
       }
   }
}
