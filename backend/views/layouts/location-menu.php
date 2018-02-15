<?php
use common\models\Location;
use yii\helpers\Url;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$location = Location::findOne(['slug' => Yii::$app->location]); ?>
        <div class="p-t-15 pull-left location-header btn btn-default" data-toggle="tooltip" data-original-title="Your location" data-placement="bottom"><i class="fa fa-map-marker m-r-10"></i><a href="<?= Url::to(['/location/view','id' => $location->id]) ?>"><?= $location->name ?></a>
        </div>
