<?php

use yii\helpers\ArrayHelper;
use common\models\UserEmail;
use common\models\Location;
$location = Location::findOne(['slug' => \Yii::$app->location]);
    $content = "";
    $model->to = "sample@example.com";
    $model->bcc = $emails;
    $data[] = "sample@example.com";
    $bccEmails = $emails;
?>

<?= $this->render('/mail/_form', [
    'content' => $content,
    'model' => $model,
    'data' => $data,
    'bccEmails' => $bccEmails,
    'subject' => $subject,
    'emailTemplate' => $emailTemplate
]);