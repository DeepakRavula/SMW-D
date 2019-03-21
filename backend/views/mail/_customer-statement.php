<?php

use yii\helpers\ArrayHelper;
use common\models\UserEmail;
use common\models\Payment;

$content = $this->renderAjax('/receive-payment/mail/view', [
    'model' => new Payment(),
    'userModel' => $user,
    'lessonLineItemsDataProvider' => $lessonLineItemsDataProvider,
    'groupLessonLineItemsDataProvider' => $groupLessonLineItemsDataProvider,
    'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
    'creditDataProvider' => $creditDataProvider,
    'emailTemplate' => $emailTemplate,
    'searchModel' => $searchModel,
    'groupLessonSearchModel' => $groupLessonSearchModel
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
    'subject' => $subject,
    'emailTemplate' => $emailTemplate
]);
