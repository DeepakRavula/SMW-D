<?php
use yii\helpers\Url;

?>
<a href="<?= Url::to(['index', 'UserSearch[role_name]' => $searchModel->role_name]);?>"><?= $roleName . 's' ;?></a>  / 
<?= $model->publicIdentity;?>