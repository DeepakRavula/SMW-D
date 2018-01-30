<?php

namespace common\models;

/**
 * This is the model class for table "blog".
 *
 * @property string $id
 * @property string $user_id
 * @property string $title
 * @property string $content
 * @property string $date
 */
class TextTemplate extends \yii\db\ActiveRecord
{
    const TYPE_PFI = 1;
    const TYPE_INVOICE = 2;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'text_template';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['message', 'type'], 'required'],
            [['message'], 'trim'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
              'message' => 'Message',
            'type' =>  'Type'
        ];
    }
    public function getType()
    {
        $type = null;
        switch ($this->type) {
            case self::TYPE_INVOICE:
                $type = 'Invoice';
                break;
            case self::TYPE_PFI:
                $type = 'Pro forma Invoice';
            break;
        }

        return $type;
    }
}
