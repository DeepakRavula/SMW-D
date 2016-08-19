<?php
return [
    'class'=>'yii\web\UrlManager',
    'enablePrettyUrl'=>true,
    'showScriptName'=>false,
    'rules'=>[
        // url rules
		'calendar/<slug:[\w-]+>' => 'calendar/view',
    ]
];
