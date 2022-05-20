<?php

use yii\helpers\ArrayHelper;
use common\models\UserEmail;
use common\models\User;

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
    'invoiceId' => $invoiceModel->id,
    'data' => $data,
    'subject' => $subject,
    'emailTemplate' => $emailTemplate,
    'userModel' => $userModel ? $userModel : new User(),
]);