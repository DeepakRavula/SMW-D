<?php

namespace common\assets\fullcalendar;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;
use yii\web\View;

class FullCalendar extends AssetBundle
{
    public $sourcePath = '@bower/fullcalendar/dist';
    public $js = [
        '/plugins/momentjs/moment-with-locales.js',
        'fullcalendar.js',
    ];
    public $css = [
        'fullcalendar.css',
        'fullcalendar.print.css'
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\jui\JuiAsset',
    ];

    public function init()
    {
        $this->jsOptions['position'] = View::POS_BEGIN;
        parent::init();
    }
}
