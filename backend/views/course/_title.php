<?php
use yii\helpers\Url;
use common\models\Lesson;

$this->title = $model->program->name;
?>
<a href="<?= Url::to(['course/index', 'LessonSearch[type]' => Lesson::TYPE_GROUP_LESSON]);?>">Group Course</a>  /
<?= $model->program->name;?>