<?php use common\models\User; ?>

<i class="fa fa-angle-down fa-lg dropdown-toggle" data-toggle="dropdown"></i>
    <ul class="dropdown-menu dropdown-menu-right" id="menu-shown">
        <li><a id="lesson-discount" href="#">Edit Discount...</a></li>
        <li><a id="edit-lesson-tax" href="#">Edit Tax...</a></li>
<?php $loggedUser = User::findOne(Yii::$app->user->id); ?>
<?php if ($loggedUser->isAdmin()) : ?>
        <li><a id="lesson-price" href="#">Edit Price...</a></li>
<?php endif; ?>
    </ul>