<?php
/**
 * @link http://www.yiiframework.com/
 *
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace frontend\assets;

use yii\web\AssetBundle;
use yii\web\View;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 *
 * @since 2.0
 */
class FrontendAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        'css/style.css',
        'plugins/jquery-timepicker/jquery.timepicker.css',
        'plugins/jquery-multiselect/style.css',
    ];

    public $js = [
        'plugins/momentjs/moment-with-locales.js',
        'plugins/jquery-timepicker/jquery.timepicker.js',
        'plugins/jquery-multiselect/multiselect.js',
        'plugins/jquery-multiselect/multiselect.min.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'common\assets\AdminLte',
        'yii\bootstrap\BootstrapAsset',
        'common\assets\Html5shiv',
    ];
    public function init()
    {
        $this->jsOptions['position'] = View::POS_BEGIN;
        parent::init();
    }
}
