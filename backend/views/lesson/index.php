<?php

use yii\bootstrap\Tabs;
use common\models\LessonReschedule;

$this->title = 'Lessons';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="tabbable-panel">
     <div class="tabbable-line">
<?php 

$indexPrivateLesson =  $this->render('_index-private-lesson', [
    'searchModel' => $searchModel,
    'dataProvider' => $dataProvider,
]);

$indexGroupLesson =  $this->render('_index-group-lesson', [
	'groupLessonSearchModel' => $groupLessonSearchModel,
	'groupLessonDataProvider' =>  $groupLessonDataProvider,
]);

?>

<?php echo Tabs::widget([
    'items' => [
		[
            'label' => 'Private Lessons',
           'content' => (int) $searchModel->type === LessonReschedule::TYPE_PRIVATE_LESSON  ? $indexPrivateLesson : null,
			'url'=>['/lesson/index','LessonSearch[type]' => LessonReschedule::TYPE_PRIVATE_LESSON],
			'active' => (int) $searchModel->type === LessonReschedule::TYPE_PRIVATE_LESSON ,    
        ],
		[
            'label' => 'Group Lessons',
            'content' => (int) $groupLessonSearchModel->type === LessonReschedule::TYPE_GROUP_LESSON  ? $indexGroupLesson : null,
			'url'=>['/lesson/index','GroupLessonSearch[type]' => LessonReschedule::TYPE_GROUP_LESSON],
			'active' => (int) $groupLessonSearchModel->type === LessonReschedule::TYPE_GROUP_LESSON ,            
        ],
    ],
]);?>
<div class="clearfix"></div>
</div>
</div>
