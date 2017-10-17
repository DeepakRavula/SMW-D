<?php
use yii\helpers\Url;

?>
<a href="<?= Url::to(['classroom/index']);?>">Classrooms</a>  / 
<?= $model->name;?>