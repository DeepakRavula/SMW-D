<?php

namespace common\models;

use Yii;



class ProformaItemLesson extends \yii\db\ActiveRecord
{
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'proforma_item_lesson';
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['lesson_id', 'safe'],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'lesson_id' => 'Lesson',
            
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @return InvoiceQuery the active query used by this AR class
     */
   
}
