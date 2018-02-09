<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */
/* @var $token string */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['sign-in/reset-password', 'token' => $token]);
?>

Dear <?php echo Html::encode($user->publicIdentity) ?>,<br>
<br>
You have requested to reset your password for your Arcadia Academy of Music account.<br>
<br>
If you follow the link below you will be able to reset your password:
<?php echo Html::a(Html::encode($resetLink), $resetLink) ?><br>
<br>
(NOTE:This link can be used just once and is scheduled to expire after 24 hours.)<br>
<br>
If you didn't make this request then you can safely ignore this email.<br>
<br>
Thank you<br>
Arcadia Academy of Music Team.<br>