<?php

namespace backend\models;

use yii\base\Model;
use Yii;
/**
 * Create user form.
 */
class EmailForm extends Model
{
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
			[['id'], 'safe']
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
