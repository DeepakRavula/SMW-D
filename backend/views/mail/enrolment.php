<?php

use yii\helpers\ArrayHelper;
use common\models\UserEmail;

    $content = $this->renderAjax('/enrolment/mail/content', [
        'toName' => $enrolmentModel->student->customer->publicIdentity,
        'content' => $content,
        'model' => $enrolmentModel,
        'lessonDataProvider' => $lessonDataProvider,
        'emailTemplate' => $emailTemplate
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
?>

<?= $this->render('/mail/_form', [
    'content' => $content,
    'model' => $model,
    'data' => $data,
    'subject' => $subject,
    'emailTemplate' => $emailTemplate
]);