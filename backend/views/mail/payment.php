<?php

use yii\helpers\ArrayHelper;
use common\models\UserEmail;

    $content = $this->renderAjax('/payment/mail_content', [
    'model' => $paymentModel,
    'lessonDataProvider' => $lessonDataProvider,
    'invoiceDataProvider' => $invoiceDataProvider,
    'emailTemplate' => $emailTemplate,
    'searchModel' => $searchModel,
    ]);
    $model->to = $emails;
    $data = null;
    if (!empty($userModel)) {
        $data = ArrayHelper::map(UserEmail::find()
            ->joinWith('userContact')
            ->andWhere(['user_contact.userId' => $userModel->id])
            ->orderBy('user_email.email')
            ->all(), 'email', 'email');
    }
?>

<?= $this->render('/mail/_form', [
    'content' => $content,
    'model' => $model,
    'paymentRequestId' => $paymentModel->id,
    'data' => $data,
    'subject' => $subject,
    'emailTemplate' => $emailTemplate
]);