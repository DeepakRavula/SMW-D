<?php

use yii\helpers\ArrayHelper;
use common\models\UserEmail;
use common\models\Location;
$locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
    $content = "";
    //$model->to = $location->email;
    $data = null;
    if (!empty($userModel)) {
        $data = ArrayHelper::map(Location::find()
            ->andWhere(['id' => $locationId])
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