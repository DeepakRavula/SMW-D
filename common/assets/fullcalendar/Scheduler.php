<?php

namespace common\assets\fullcalendar;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class Scheduler extends AssetBundle
{
    public $sourcePath = '@bower/fullcalendar-scheduler/dist';
    public $js = [
        'scheduler.js',
    ];

    public $css = [
        'scheduler.css'
    ];
    public $depends = [
        \common\assets\fullcalendar\FullCalendar::class
    ];
}
