<?php

use yii\helpers\ArrayHelper;
use common\models\UserEmail;

    $content = $this->renderAjax('/receive-payment/_mail-content', [
    'lessonLineItemsDataProvider' => $lessonLineItemsDataProvider,
    'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
    'paymentsLineItemsDataProvider'  =>  $paymentsLineItemsDataProvider,
    'emailTemplate' => $emailTemplate,
    'searchModel' => $searchModel,
    'customer' => $customer,
    ]);
    $model->to = $emails;
    $data = null;
    if (!empty($customer)) {
        $data = ArrayHelper::map(UserEmail::find()
            ->joinWith('userContact')
            ->andWhere(['user_contact.userId' => $customer->id])
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