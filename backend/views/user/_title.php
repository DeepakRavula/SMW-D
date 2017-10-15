<?php
use yii\helpers\Url;

?>
<a href="<?= Url::to(['index', 'UserSearch[role_name]' => $searchModel->role_name]);?>"><?= ucwords($searchModel->role_name) . 's' ;?></a>  / 
<?= $model->publicIdentity;?>