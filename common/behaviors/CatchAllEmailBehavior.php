<?php
namespace common\behaviors;

use Yii;
use yii\base\Behavior;
use yii\mail\BaseMailer;
use common\models\TestEmail;
use backend\models\EmailForm;
use common\models\PasswordResetRequestForm;

/**
 * Class CatchAllMailBehavior
 * @package common\behaviors
 */
class CatchAllEmailBehavior extends Behavior
{

    /**
     * @return array
     */
    public function events()
    {
        return [
            BaseMailer::EVENT_BEFORE_SEND => 'addTestEmail',
        ];
    }

    public function addTestEmail($event) {
		$model = new EmailForm();
		$passwordResetModel = new PasswordResetRequestForm();
		if (env('YII_ENV') === 'dev') {
			$email = TestEmail::find()->one()->email;
		} else {
			$model->load(Yii::$app->request->post());
			$email = $model->to;
			$passwordResetModel->load(Yii::$app->request->post());
			$email = $passwordResetModel->email;
		}
		return $event->message->setTo($email);
	}

}