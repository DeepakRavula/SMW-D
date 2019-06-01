<?php
use yii\helpers\Url;
use common\models\Lesson;
use common\models\User;

?>

<?php if (!$model->isStaff()) :?>
<a href="<?= Url::to(['user/index', 'UserSearch[role_name]' => User::ROLE_CUSTOMER]);?>">Customers</a>
/
<a href="<?= Url::to(['report/account-receivable']);?>">Accounts Receivable</a>
/ 
<a href="<?= Url::to(['user/view', 'UserSearch[role_name]' => User::ROLE_CUSTOMER, 'id' => $model->id]);?>"><?= $model->publicIdentity;?></a>
<?php endif; ?>