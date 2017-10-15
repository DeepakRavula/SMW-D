<?php
use yii\helpers\Url;
use common\models\Lesson;
?>
<a href="<?= Url::to(['index', 'LessonSearch[type]' => Lesson::TYPE_PRIVATE_LESSON]);?>">Lessons</a>  / 
<?= $model->course->program->name;?>
<span class="m-l-10"><?= $model->course->program->isPrivate() ? '<i title="Private" class="fa fa-lock"></i>' : '<i title="Group" class="fa fa-users"></i>';?></span>