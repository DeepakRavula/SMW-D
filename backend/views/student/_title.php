<?php
use yii\helpers\Url;

?>
<a href="<?= Url::to(['student/index', 'StudentSearch[showAllStudents]' => false]);?>">Students</a>  / 
<?= $model->fullName;?>