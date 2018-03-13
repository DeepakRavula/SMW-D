<?php

namespace backend\models;

use yii\base\Model;
use Yii;

/**
 * Create user form.
 */
class EmailForm extends Model
{
    public $invoiceId;
    public $to;
    public $subject;
    public $content;
    public $id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['to', 'subject', 'content'], 'required'],
            [['invoiceId'], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'to' => Yii::t('backend', 'To'),
            'subject' => Yii::t('backend', 'Subject'),
            'content' => Yii::t('backend', 'Content'),
        ];
    }
}
