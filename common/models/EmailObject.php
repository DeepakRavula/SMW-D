<?php

namespace common\models;

/**
 * This is the model class for table "email-object".
 *
 * @property string $id
 * @property string $name
 */
class EmailObject extends \yii\db\ActiveRecord
{
    const OBJECT_COURSE = 1;
    const OBJECT_LESSON = 2;
    const OBJECT_INVOICE = 4;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'email_object';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'trim'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Type',
        ];
    }
}