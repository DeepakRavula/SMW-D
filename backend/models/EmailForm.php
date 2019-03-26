<?php

namespace backend\models;

use yii\base\Model;
use Yii;
use common\models\EmailMultiCustomer;

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
    public $bcc;
    public $paymentRequestId;
    public $objectId;
    public $userId;
    const ENVIRONMENT_NAME = 'develoment';
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['subject', 'content'], 'required'],
            [['to'], 'required', 'except' => EmailMultiCustomer::SCENARIO_SEND_EMAIL_MULTICUSTOMER],
            [['invoiceId', 'paymentRequestId', 'bcc', 'objectId', 'userId'], 'safe'],
            [['bcc'], 'required', 'on' => EmailMultiCustomer::SCENARIO_SEND_EMAIL_MULTICUSTOMER],
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
