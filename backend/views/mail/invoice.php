<?php

use yii\helpers\ArrayHelper;
use common\models\UserEmail;

    $content = $this->renderAjax('/invoice/mail/content', [
        'model' => $invoiceModel,
        'searchModel' => $searchModel,
        'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
        'emailTemplate' => $emailTemplate,
        'invoicePaymentsDataProvider' => $invoicePaymentsDataProvider,
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
    'data' => $data,
    'subject' => $subject,
    'emailTemplate' => $emailTemplate
]);