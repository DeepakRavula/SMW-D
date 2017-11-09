<?php 

use yii\helpers\ArrayHelper;
use common\models\User;
?>

<?php
    $roles = ArrayHelper::getColumn(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id), 'name');
    $role = end($roles);
?>
    
<?php if ($role === User::ROLE_ADMINISTRATOR) : ?>
    <i title="Edit" class="m-r-10 fa fa-pencil edit-enrolment-rate"></i>
<?php endif; ?>