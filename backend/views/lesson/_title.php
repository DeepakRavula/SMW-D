<?php
use yii\helpers\Url;
use common\models\Lesson;

?>
<?php if ($model->course->program->isPrivate()) :?>
	<a href="<?= Url::to(['index', 'LessonSearch[type]' => Lesson::TYPE_PRIVATE_LESSON]);?>">Private Lessons</a>
<?php else :?>
	<a href="<?= Url::to(['index', 'LessonSearch[type]' => Lesson::TYPE_GROUP_LESSON]);?>">Group Lessons</a>	
<?php endif; ?>	/ 
<?= $model->course->program->name;?>