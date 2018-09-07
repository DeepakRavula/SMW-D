<?php

use yii\helpers\ArrayHelper;
use common\models\UserEmail;

   // $body = null;
        $body = $this->renderAjax('/lesson/mail/body', [
            'model' => $lessonModel,
        ]);
    $content = $this->renderAjax('/lesson/mail/content', [
        'content' => $body,
        'emailTemplate' => $emailTemplate
    ]);
    $model->to = $emails;
    $data = null;
    if (!empty($userModel)) {
        $data = ArrayHelper::map(UserEmail::find()
            ->notDeleted()
            ->joinWith('userContact')
            ->andWhere(['user_contact.userId' => $userModel->id])
            ->orderBy('user_email.email')
            ->all(), 'email', 'email');
    }
?>

<?= $this->render('/mail/_form', [
    'content' => $content,
    'model' => $model,
    'data' => $data,
    'subject' => $subject,
    'emailTemplate' => $emailTemplate
]);