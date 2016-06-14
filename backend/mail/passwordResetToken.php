<?php
use yii\helpers\Html;
use common\models\User;

/* @var $this yii\web\View */
/* @var $user common\models\User */
/* @var $token string */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['sign-in/reset-password', 'token' => $token]);
?>

Dear <?php echo Html::encode($user->publicIdentity) ?>,<br>
<br>
You have requested to reset your password for your Arcadia Music Academy account.<br>
<br>
If you follow the link below you will be able to personally reset your password:
<?php echo Html::a(Html::encode($resetLink), $resetLink) ?><br>
<br>
(NOTE:This link is good until today and can only be used once)<br>
<br>
If you didn't make this request then you can safely ignored this email.<br>
<br>
Thank you<br>
Arcadia Music Academy Team.<br>