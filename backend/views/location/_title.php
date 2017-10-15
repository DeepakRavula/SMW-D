<?php
use yii\helpers\Url;

?>
<a href="<?= Url::to(['location/index']);?>">Locations</a>  / 
<?= $model->name;?>