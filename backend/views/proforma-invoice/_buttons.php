<?php

use yii\helpers\Html;
use common\models\User;
use backend\models\search\InvoiceSearch;
use yii\widgets\Pjax;

?>
<?php $loggedUser = User::findOne(Yii::$app->user->id); ?>
<?php Pjax::Begin(['id' => 'invoice-header-summary']) ?>
<div id="invoice-header">

	<?= Html::a('<i title="Delete" class="fa fa-trash"></i>', ['#', 'id' => $model->id], [
            'class' => 'm-r-10 btn btn-box-tool',
            'id' => 'proforma-invoice-delete-button',
        ])?>

<?= Html::a('<i title="Mail" class="fa fa-envelope-o"></i>', '#', [
    'id' => 'invoice-mail-button',
    'class' => 'm-r-10 btn btn-box-tool']) ?>
<?= Html::a('<i class="fa fa-print m-r-10"></i>', ['#'], ['class' => 'm-r-10 btn btn-box-tool','id'=>'print-btn']) ?>
<?= Yii::$app->formatter->format($model->getTotal($model->id), ['currency', 'USD', [
    \NumberFormatter::MIN_FRACTION_DIGITS => 2,
    \NumberFormatter::MAX_FRACTION_DIGITS => 2,
]]); ?> &nbsp;&nbsp;
</div>
<?php Pjax::end();?>