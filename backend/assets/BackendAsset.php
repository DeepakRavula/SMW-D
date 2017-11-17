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
		'css/dashboard/style.css',
		'css/calendar/style.css',
        'css/invoice/style.css',
        'css/teacher/style.css',
        'plugins/jquery-timepicker/jquery.timepicker.css',
        'plugins/jquery-multiselect/style.css',
		'plugins/bootstrap-toggle/css/bootstrap-toggle.css',
    ];
    public $js = [
        'plugins/momentjs/moment-with-locales.js',
        'plugins/jquery-timepicker/jquery.timepicker.js',
        'plugins/jquery-multiselect/multiselect.js',
        'plugins/jquery-multiselect/multiselect.min.js',
		'plugins/bootstrap-toggle/js/bootstrap-toggle.min.js'
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
