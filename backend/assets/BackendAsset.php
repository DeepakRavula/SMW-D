<?php
/**
 * Created by PhpStorm.
 * User: zein
 * Date: 7/3/14
 * Time: 3:14 PM
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
        'plugins/fullcalendar/fullcalendar.css',
        'plugins/jquery-timepicker/jquery.timepicker.css'
    ];
    public $js = [
        'js/app.js',
        'plugins/momentjs/moment-with-locales.js',
        'plugins/fullcalendar/fullcalendar.js',
        'plugins/jquery-timepicker/jquery.timepicker.js',
        'plugins/history/jquery.history.js'
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'common\assets\AdminLte',
        'common\assets\Html5shiv'
    ];
    
    public function init() {
        $this->jsOptions['position'] = View::POS_BEGIN;
        parent::init();
    }
}
