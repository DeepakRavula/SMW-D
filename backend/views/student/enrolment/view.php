<?php

use yii\bootstrap\Tabs;
use common\models\Course;
use yii\helpers\Url;
use common\models\User;

/* @var $this yii\web\View */
/* @var $model common\models\Student */
$this->title = 'Student Enrolment';

?>
<?php 

$privateCourseContent = $this->render('_form-private', [
    'model' => new Course(),
]);

$groupCourseContent = $this->render('_form-group', [
    'groupCourseDataProvider' => $groupCourseDataProvider,
]);

?>
<?php echo Tabs::widget([
    'items' => [
        [
            'label' => 'Private',
            'content' => $privateCourseContent,
            'options' => [
                    'id' => 'private',
                ],
        ],
        [
            'label' => 'Group',
            'content' => $groupCourseContent,
            'options' => [
                    'id' => 'group',
                ],
        ],
    ],
]);
?>