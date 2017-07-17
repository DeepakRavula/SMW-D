<?php
/**
 * Created by PhpStorm.
 * User: zein
 * Date: 7/3/14
 * Time: 3:14 PM.
 */

namespace backend\assets;

use yii\web\AssetBundle;
use yii\web\View;

class BackendAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        'css/style.css',
        'plugins/jquery-timepicker/jquery.timepicker.css',
        'plugins/jquery-multiselect/style.css',
//		'plugins/fullcalendar-scheduler/lib/fullcalendar.print.min.css',
//		'plugins/fullcalendar-scheduler/scheduler.css',
//		'plugins/bootstrap-datepicker/bootstrap-datepicker.css',
    ];
    public $js = [
        'plugins/momentjs/moment-with-locales.js',
        'plugins/jquery-timepicker/jquery.timepicker.js',
        'plugins/jquery-multiselect/multiselect.js',
        'plugins/jquery-multiselect/multiselect.min.js',
//		'plugins/fullcalendar-scheduler/lib/fullcalendar.min.js',
//		'plugins/fullcalendar-scheduler/scheduler.js',
//		'plugins/bootstrap-datepicker/bootstrap-datepicker.js'	
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'common\assets\AdminLte',
        'common\assets\Html5shiv',
        'common\assets\MarionetteJS',
    ];

    public function init()
    {
        $this->jsOptions['position'] = View::POS_BEGIN;
        parent::init();
    }
}
