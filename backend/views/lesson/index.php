<?php

use yii\bootstrap\Tabs;
use common\models\Lesson;

$this->title = 'Lessons';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="tabbable-panel">
     <div class="tabbable-line">
<?php 

$indexPrivateLesson = $this->render('_index-lesson', [
    'searchModel' => $searchModel,
    'dataProvider' => $dataProvider,
]);

$indexGroupLesson = $this->render('_index-lesson', [
    'searchModel' => $searchModel,
    'dataProvider' => $dataProvider,
]);

?>

<?php echo Tabs::widget([
    'items' => [
        [
            'label' => 'Private Lessons',
           'content' => (int) $searchModel->type === Lesson::TYPE_PRIVATE_LESSON ? $indexPrivateLesson : null,
            'url' => ['/lesson/index', 'LessonSearch[type]' => Lesson::TYPE_PRIVATE_LESSON],
            'active' => (int) $searchModel->type === Lesson::TYPE_PRIVATE_LESSON,
        ],
        [
            'label' => 'Group Lessons',
            'content' => (int) $searchModel->type === Lesson::TYPE_GROUP_LESSON ? $indexGroupLesson : null,
            'url' => ['/lesson/index', 'LessonSearch[type]' => Lesson::TYPE_GROUP_LESSON],
            'active' => (int) $searchModel->type === Lesson::TYPE_GROUP_LESSON,
        ],
    ],
]); ?>
<div class="clearfix"></div>
</div>
</div>
