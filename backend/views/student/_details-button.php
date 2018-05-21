<?php 

use common\models\User;

?>

<i title="Edit" class="fa fa-pencil student-profile-edit-button m-r-10"></i>

<?php $loggedUser = User::findOne(Yii::$app->user->id); ?>

<?php if ($loggedUser->canMerge) : ?>
    <i class="fa fa-angle-down fa-lg dropdown-toggle" data-toggle="dropdown"></i>
    <ul class="dropdown-menu dropdown-menu-right" id="menu-shown">
        <li><a id="student-merge" href="#">Merge</a></li>
    </ul>
<?php endif; ?>