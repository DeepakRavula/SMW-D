<?php

namespace common\models;
use yii\behaviors\TimestampBehavior;
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
    /**
     * {@inheritdoc}
     */
    const COURSE='Course';
    const LESSON='Lesson';
    const INVOICE='Invoice';
    const PROFORMA_INVOICE='ProformaInvoice';
    const CUSTOMERSTATEMENT = 'CustomerStatement';
    
    public static function tableName()
    {
        return 'email_template';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'createdAt',
                'updatedAtAttribute' => 'updatedAt',
                'value' => (new \DateTime())->format('Y-m-d H:i:s'),
            ],
            
        ];
    }
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
