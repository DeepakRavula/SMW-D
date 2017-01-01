<?php

namespace common\assets\fullcalendar;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class FullCalendar extends AssetBundle
{
    public $sourcePath = '@bower/fullcalendar/dist';
    public $js = [
		'fullcalendar.js',
    ];
	public $css = [
		'fullcalendar.css',
		'fullcalendar.print.css'
	];
}
