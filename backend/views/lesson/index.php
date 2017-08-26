<?php

use yii\bootstrap\Tabs;
use common\models\Lesson;
use backend\models\search\CourseSearch;

$this->title = 'Lessons';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="nav-tabs-custom">
<?php 

$indexPrivateLesson = $this->render('_index-lesson', [
    'searchModel' => $searchModel,
    'dataProvider' => $dataProvider,
]);

$courseSearchModel = new CourseSearch();
$dataProvider = $courseSearchModel->search(Yii::$app->request->queryParams);

$indexGroupLesson = $this->render('/course/index', [
	'searchModel' => $courseSearchModel,
	'dataProvider' => $dataProvider,
]);

?>

<?php echo Tabs::widget([
    'items' => [
        [
            'label' => 'Private',
           'content' => $indexPrivateLesson,
            'options' => [
				'id' => 'private',
			],
        ],
        [
            'label' => 'Group',
            'content' => $indexGroupLesson,
            'options' => [
				'id' => 'group',
			],
        ],
    ],
]); ?>
<div class="clearfix"></div>
</div>
