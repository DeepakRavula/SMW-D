<?php

use yii\helpers\ArrayHelper;
use common\models\UserEmail;
use common\models\Payment;

$content = $this->renderAjax('/course/mail-content', [
   'model' => $enrolmentModel,
   'lessonDataProvider' => $lessonDataProvider,
]);
$model->to = $emails;
$data = [];
if (!empty($userModel)) {
    $data = ArrayHelper::map(UserEmail::find()
        ->notDeleted()
        ->joinWith('userContact')
        ->andWhere(['user_contact.userId' => $userModel->id])
        ->orderBy('user_email.email')
        ->all(), 'email', 'email');
}
$model->to = !empty($data) ? $data : null;
?>
<?= $this->render('/mail/_form', [
    'content' => $content,
    'model' => $model,
    'subject' => $subject,
    'emailTemplate' => $emailTemplate,
    'data' => $data,
    'userModel' => $userModel
]);