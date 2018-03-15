<?php
use yii\helpers\Url;
use common\models\Lesson;

?>
<a href="<?= Url::to(['lesson/index', 'LessonSearch[type]' => Lesson::TYPE_GROUP_LESSON]);?>">Group Lessons</a>  /
<?= $model->program->name;?>