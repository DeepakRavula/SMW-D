<?php
use yii\helpers\Html;
use common\models\User;

$loggedUser = User::findOne(Yii::$app->user->id);
$user = User::findOne($model->id);
$button = Html::a('<i title="Delete" class="fa fa-trash"></i>', ['delete', 'id' => $model->id], [
            'class' => 'm-r-10 btn btn-box-tool user-delete-button',
        ]);

if ($loggedUser->isAdmin()) {
    echo $button;
} else if ($loggedUser->isOwner() && $user->isManagableByOwner()) {
    echo $button;
} else if ($loggedUser->isStaff() && $user->isManagableByStaff()) {
    echo $button;
}