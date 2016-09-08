<?php
return [
    'class'=>'yii\web\UrlManager',
    'enablePrettyUrl'=>true,
    'showScriptName'=>false,
    'rules'=>[
        // url rules
		'calendar/<slug:[\w-]+>' => 'calendar/view',
        'group-course/<groupCourseId:\d+>/student/<studentId:\d+>' => 'group-course/view-student',
        'student/<studentId:\d+>/enrolment/<enrolmentId:\d+>/program-type/<programType:\d+>/delete-preview' => 'student/delete-enrolment-preview',
		'student/<studentId:\d+>/delete-preview' => 'student/delete-student-preview',
		'student/<studentId:\d+>/delete' => 'student/delete-student',
    ]
];
