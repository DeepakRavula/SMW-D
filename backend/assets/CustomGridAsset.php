<?php
namespace backend\assets;
use yii\web\AssetBundle;

class CustomgridAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [

    ];
    public $js = [
        'js/kv-grid-group.js',
    ];
}
?>