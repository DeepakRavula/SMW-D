<?php

namespace common\models;

/**
 * This is the model class for table "phone_label".
 *
 * @property int $id
 * @property string $name
 */
class PhoneLabel extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'phone_label';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 32],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
        ];
    }

    public static function findByPhoneLabel($user_id)
    {
        return static::find()
            ->join('INNER JOIN', 'phone_number', 'phone_number.label_id = phone_label.id')
            ->where(['phone_number.user_id' => $user_id])
            ->all();
    }
}
