<?php

namespace common\assets;

use yii\web\AssetBundle;

/**
 * Class JquerySlimScroll.
 *
 * @author Eugene Terentev <eugene@terentev.net>
 */
class RequireJS extends AssetBundle
{
    public $sourcePath = '@bower/requirejs';
    public $js = [
        'require.js',
    ];
	public $jsOptions = [
		'data-main' => '/js/app.js',
	];
}

