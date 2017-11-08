<?php
use yii\helpers\Html;
use common\models\User;
?>
<?php

$loggedInUserRole = $model->getRoleById(Yii::$app->user->id);
$viewedUserRole = $model->getRoleById($model->id);
$button = Html::a('<i title="Delete" class="fa fa-trash"></i>', ['delete', 'id' => $model->id], [
            'class' => 'm-r-10 btn btn-box-tool user-delete-button',
        ]);

if ($loggedInUserRole !== $viewedUserRole) {
    if ($loggedInUserRole === User::ROLE_ADMINISTRATOR) {
        echo $button;
    } else if ($loggedInUserRole === User::ROLE_OWNER) {
        if ($viewedUserRole === User::ROLE_CUSTOMER || $viewedUserRole === User::ROLE_TEACHER || $viewedUserRole === User::ROLE_STAFFMEMBER) {
            echo $button;
        }
    } else if ($loggedInUserRole === User::ROLE_STAFFMEMBER) {
        if ($viewedUserRole === User::ROLE_CUSTOMER || $viewedUserRole === User::ROLE_TEACHER) {
            echo $button;
        }
    }
}
?>