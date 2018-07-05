<?php

use yii\helpers\Html;
use common\models\User;
use backend\models\search\InvoiceSearch;
use yii\widgets\Pjax;

?>
<?php $loggedUser = User::findOne(Yii::$app->user->id); ?>
<?php Pjax::Begin(['id' => 'invoice-header-summary']) ?>
<div id="invoice-header">
<?php if ((int) $model->type === InvoiceSearch::TYPE_PRO_FORMA_INVOICE): ?>
    <?php if ((bool) !$model->isDeleted()): ?>
	<?= Html::a('<i title="Delete" class="fa fa-trash"></i>', ['delete', 'id' => $model->id], [
            'class' => 'm-r-10 btn btn-box-tool',
            'id' => 'invoice-delete-button',
        ])?>
    <?php endif; ?>
<?php endif; ?>
<div class="m-b-10 pull-right">
    <?= $this->render('_more-option', [
        'model' => $model,
        'loggedUser' => $loggedUser
    ]) ?>
</div>
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
<?php endif; ?>
<?= Html::a('<i title="Mail" class="fa fa-envelope-o"></i>', '#', [
    'id' => 'invoice-mail-button',
    'class' => 'm-r-10 btn btn-box-tool']) ?>
<?= Html::a('<i class="fa fa-print m-r-10"></i>', ['#'], ['class' => 'm-r-10 btn btn-box-tool','id'=>'print-btn']) ?>
<?= strtoupper($model->getStatus()) . ' '?>
<?= Yii::$app->formatter->format($model->total, ['currency', 'USD', [
    \NumberFormatter::MIN_FRACTION_DIGITS => 2,
    \NumberFormatter::MAX_FRACTION_DIGITS => 2,
]]); ?> &nbsp;&nbsp;
</div>
<?php Pjax::end();?>