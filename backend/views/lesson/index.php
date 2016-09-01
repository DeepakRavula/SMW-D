<?php

use yii\bootstrap\Tabs;

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
			'groupLessonDataProvider' => $groupLessonDataProvider,
]);

?>

<?php echo Tabs::widget([
    'items' => [
		[
            'label' => 'Private Lessons',
            'content' => $indexPrivateLesson,
			'options' => [
				'id' => 'private-lesson',
			]
        ],
		[
            'label' => 'Group Lessons',
            'content' => $indexGroupLesson ,
			'options' => [
				'id' => 'group-lesson',
			]
        ],
    ],
]);?>
<div class="clearfix"></div>
</div>
</div>
