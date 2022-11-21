<?php

use yii\helpers\Html;
use common\models\User;
use backend\models\search\InvoiceSearch;
use yii\widgets\Pjax;
use common\models\Invoice;
use common\models\InvoiceReverse;

?>
<?php $loggedUser = User::findOne(Yii::$app->user->id); ?>
<?php Pjax::Begin(['id' => 'invoice-header-summary']) ?>
<div id="invoice-header">
<?php if ((int) $model->type === InvoiceSearch::TYPE_INVOICE): ?>
    <div class="m-b-10 pull-right">
    <?= $this->render('_more-option', [
        'model' => $model,
        'loggedUser' => $loggedUser
    ]) ?>
</div>
    <?php endif;?>
<?php if ((int) $model->type === InvoiceSearch::TYPE_INVOICE): ?>
    <?php if ($model->canRevert()): ?>
        <?=	Html::a(
            '<i title="Return" class="fa fa-reply"></i>',
            ['revert-invoice', 'id' => $model->id],
        [
            'class' => 'm-r-10 btn btn-box-tool',
            'data' => [
                'confirm' => 'Are you sure you want to return this invoice?',
            ],
            'id' => 'revert-button',
        ]
        )
        ?>
    <?php endif; ?>
    <?php if (!empty($model->reversedInvoice)) : ?>
        <span class="return-invoice m-r-10"></span>
    <?php endif; ?>
    <?php if ($model->isCanceled): ?>
            <?= Html::a('Returned '.$model->returnedInvoice->getInvoiceNumber(), ['invoice/view', 'id' => $model->returnedInvoice->id]); ?>
    <?php endif; ?>
<?php endif; ?>
<?php if ((int) $model->type === InvoiceSearch::TYPE_INVOICE): ?>
<?= Html::a('<i title="Mail" class="fa fa-envelope-o"></i>', '#', [
    'id' => 'invoice-mail-button',
    'class' => 'm-r-10 btn btn-box-tool']) ?>
<?= Html::a('<i class="fa fa-print m-r-10"></i>', ['#'], ['class' => 'm-r-10 btn btn-box-tool','id'=>'print-btn']) ?>
<?= strtoupper($model->getStatus()) . ' '?>
<?php endif; ?>
<?= Yii::$app->formatter->format(round($model->total, 2), ['currency', 'USD', [
    \NumberFormatter::MIN_FRACTION_DIGITS => 2,
    \NumberFormatter::MAX_FRACTION_DIGITS => 2,
]]); ?> &nbsp;&nbsp;
</div>
<?php Pjax::end();?>