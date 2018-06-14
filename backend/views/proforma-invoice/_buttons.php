<?php

use yii\helpers\Html;
use common\models\User;
use backend\models\search\InvoiceSearch;
use yii\widgets\Pjax;

?>
<?php $loggedUser = User::findOne(Yii::$app->user->id); ?>
<?php Pjax::Begin(['id' => 'invoice-header-summary']) ?>
<div id="invoice-header">

<?= Yii::$app->formatter->format($model->getTotal($model->id), ['currency', 'USD', [
    \NumberFormatter::MIN_FRACTION_DIGITS => 2,
    \NumberFormatter::MAX_FRACTION_DIGITS => 2,
]]); ?> &nbsp;&nbsp;
</div>
<?php Pjax::end();?>