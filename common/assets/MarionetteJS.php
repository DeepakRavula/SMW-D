<?php

namespace common\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class MarionetteJS extends AssetBundle
{
    public $sourcePath = '@bower';
    public $js = [
        'underscore/underscore.js',
        'backbone/backbone.js',
        'backbone.radio/build/backbone.radio.js',
        'marionette/lib/backbone.marionette.js',
    ];
}
