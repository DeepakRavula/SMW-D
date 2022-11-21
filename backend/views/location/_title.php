<?php
use yii\helpers\Url;
use common\models\User;

?>
<?php if(Yii::$app->user->can(User::ROLE_ADMINISTRATOR)) : ?>
    <a href="<?= Url::to(['location/index']);?>">Locations</a>  / 
<?php endif; ?>
<?= $model->name;?>