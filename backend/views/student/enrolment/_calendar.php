<?php

use yii\helpers\Url;
use common\models\LocationAvailability;

require_once Yii::$app->basePath . '/web/plugins/fullcalendar-time-picker/modal-popup.php';

?>
<?= $this->render('/lesson/_color-code');?>
<div id="enrolment-calendar">
    <div id="private-enrolment-spinner" class="spinner m-t-25" style="display:none">
        <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
        <span class="sr-only">Loading...</span>
    </div>
</div>
<?php
    $locationId = \common\models\Location::findOne(['slug' => \Yii::$app->location])->id;
    $minLocationAvailability = LocationAvailability::find()
        ->andWhere(['locationId' => $locationId])
        ->orderBy(['fromTime' => SORT_ASC])
        ->one();
    $maxLocationAvailability = LocationAvailability::find()
        ->andWhere(['locationId' => $locationId])
        ->orderBy(['toTime' => SORT_DESC])
        ->one();
    $from_time = (new \DateTime($minLocationAvailability->fromTime))->format('H:i:s');
    $to_time = (new \DateTime($maxLocationAvailability->toTime))->format('H:i:s');
?>
