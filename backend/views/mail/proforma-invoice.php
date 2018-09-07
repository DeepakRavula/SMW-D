<?php

use yii\helpers\ArrayHelper;
use common\models\UserEmail;

    $content = $this->renderAjax('/proforma-invoice/mail/content', [
    'model' => $proformaInvoiceModel,
    'lessonLineItemsDataProvider' => $lessonLineItemsDataProvider,
    'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
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
?>

<?= $this->render('/mail/_form', [
    'content' => $content,
    'model' => $model,
    'paymentRequestId' => $proformaInvoiceModel->id,
    'data' => $data,
    'subject' => $subject,
    'emailTemplate' => $emailTemplate
]);