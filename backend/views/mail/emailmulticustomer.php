<?php

use yii\helpers\ArrayHelper;
use common\models\UserEmail;
use common\models\Location;
$location = Location::findOne(['slug' => \Yii::$app->location]);
    $content = "";
    $model->to = $emails;
    $model->bcc = "";
    $data = "";
    $bccEmails = $emails;
?>

<?= $this->render('/mail/_form-multi-customer', [
    'content' => $content,
    'model' => $model,
    'data' => $data,
    'bccEmails' => $bccEmails,
    'subject' => $subject,
    'emailTemplate' => $emailTemplate
]);