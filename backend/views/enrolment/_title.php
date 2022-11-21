<?php
use yii\helpers\Url;

?>
<a href="<?= Url::to(['enrolment/index', 'EnrolmentSearch[showAllEnrolments]' => false]);?>">Enrolments</a>  / 
<?= $model->course->program->name;?>
<span class="m-l-10"><?= $model->course->program->isPrivate() ? '<i title="Private" class="fa fa-lock"></i>' : '<i title="Group" class="fa fa-users"></i>';?></span>