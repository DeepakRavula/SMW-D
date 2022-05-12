<?php

use yii\helpers\ArrayHelper;
use common\models\UserEmail;

    $content = $this->renderAjax('/payment/mail_content', [
    'model' => $paymentModel,
    'lessonDataProvider' => $lessonDataProvider,
    'groupLessonDataProvider' =>  $groupLessonDataProvider,
    'invoiceDataProvider' => $invoiceDataProvider,
    'emailTemplate' => $emailTemplate,
    'searchModel' => $searchModel,
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
    $model->to = !empty($data) ? $data : null;
?>

<?= $this->render('/mail/_form', [
    'content' => $content,
    'model' => $model,
    'paymentRequestId' => $paymentModel->id,
    'data' => $data,
    'subject' => $subject,
    'emailTemplate' => $emailTemplate,
    'userModel' => $userModel,
    'paymentId' => $paymentModel->id,
]);