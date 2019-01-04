<?php

use yii\helpers\ArrayHelper;
use common\models\UserEmail;
use common\models\Location;
$location = Location::findOne(['slug' => \Yii::$app->location]);
    $content = "";
    //$model->to = $location->email;
    $data [] = $location->email;
    
        $bccEmails = ArrayHelper::map(Location::find()
            ->andWhere(['id' => $locationId])
            ->all(), 'email', 'email');
?>

<?= $this->render('/mail/_form', [
    'content' => $content,
    'model' => $model,
    'data' => $data,
    'bccEmails' => $bccEmails,
    'subject' => $subject,
    'emailTemplate' => $emailTemplate
]);