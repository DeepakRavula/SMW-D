<?php
use yii\helpers\Url;
use yii\widgets\Pjax;
?>
<?php Pjax::Begin(['id' => 'user-header'])?>
<a href="<?= Url::to(['index', 'UserSearch[role_name]' => $searchModel->role_name]);?>"><?= $roleName . 's' ;?></a>  / 
<span class="m-r-10"><?= $model->publicIdentity;?></span>
<img src='<?= $model->userProfile->getAvatar();?>' alt="user image" class="user-profile img-circle offline">
<?php Pjax::end();?>