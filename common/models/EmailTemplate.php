<?php

namespace common\models;

/**
 * This is the model class for table "emailtemplate".
 *
 * @property string $id
 * @property string $user_id
 * @property string $title
 * @property string $content
 * @property string $date
 */
class EmailTemplate extends \yii\db\ActiveRecord
{
    const TYPE_PFI = 1;
    const TYPE_INVOICE = 2;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'email_template';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['emailTypeId', 'subject' , 'header' , 'footer'], 'required'],
            [['subject'], 'trim'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'emailTypeId' => 'Type',
            'subject' =>  'Subject',
            'header' => 'Header',
            'footer' => 'Footer',
        ];
    }
    
     public function getEmailObject() {
         return $this->hasOne(EmailObject::Classname(),['id' => 'emailTypeId']);
     }
}
