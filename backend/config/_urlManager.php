<?php
return [
    'class'=>'yii\web\UrlManager',
    'enablePrettyUrl'=>true,
    'showScriptName'=>false,
    'rules'=>[
        // url rules
		'calendar/<slug:[\w-]+>' => 'calendar/view',
        'group-course/<groupCourseId:\d+>/student/<studentId:\d+>' => 'group-course/view-student'
    ]
];
