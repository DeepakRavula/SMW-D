<?php

namespace common\components\queue;

use Yii;
use yii\base\BaseObject;
use yii\queue\RetryableJobInterface;

/**
 * Class OderNotification.
 */
class BulkEmail extends BaseObject implements RetryableJobInterface
{
    public $locationEmail;
    public $content;
    public $subject;
    public $to;

    /**
     * @inheritdoc
     */
    public function execute($queue)
    {
        $content = [];
        $content[] = Yii::$app->mailer->compose('content', [
            'content' => $this->content,
        ])
        ->setFrom($this->locationEmail)
        ->setReplyTo($this->locationEmail)
        ->setSubject($this->subject)
        ->setTo($this->to);
        Yii::$app->mailer->sendMultiple($content);
        
        return true;
    }
    /**
     * @inheritdoc
     */
    public function getTtr()
    {
        return 60;
    }
    /**
     * @inheritdoc
     */
    public function canRetry($attempt, $error)
    {
        return $attempt < 1;
    }
}
