<?php
use yii\helpers\Url;
use common\models\Location;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$location = Location::findOne(['slug' => Yii::$app->location]);
?>
<div class="p-t-15 pull-left staff-location-header" data-toggle="tooltip" data-original-title="Your location" data-placement="bottom"><i class="fa fa-map-marker m-r-10"></i><?= $location->name; ?>
</div>