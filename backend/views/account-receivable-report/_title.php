<?php
use yii\helpers\Url;
use common\models\Lesson;

?>

	<a href="<?= Url::to(['report/account-receivable']);?>">Accounts Receivable</a>
/ 
<?= $model->publicIdentity;?>