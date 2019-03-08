<?php

use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use common\models\Course;
use yii\widgets\Pjax;
use yii\helpers\Url;

?>
<?php Pjax::begin([
    'id' => 'lesson-price-details',
    'timeout' => 6000,
]) ?>
<?php
LteBox::begin([
    'type' => LteConst::TYPE_DEFAULT,
    'boxTools' => $this->render('_discount-action-menu', [
        'model' => $model,
    ]),
    'title' => 'Totals',
    'withBorder' => true,
])
?>
<dl class="dl-horizontal lesson-discount">
	<dt class=" m-r-10">Lesson Rate/hr</dt>
	<dd class = "total-horizontal-dd text-right"><?= Yii::$app->formatter->asCurrency($model->programRate); ?></dd>
	<dt class="m-r-10">Qty</dt>
    <dd class = "total-horizontal-dd text-right"><?= $model->unit; ?></dd>
    <dt class="m-r-10">Lesson Price</dt>
    <dd class = "total-horizontal-dd text-right"><?= Yii::$app->formatter->asCurrency(round($model->grossPrice, 2)); ?></dd>
    <dt class="m-r-10">Discount</dt>
	<dd class = "total-horizontal-dd text-right"><?= Yii::$app->formatter->asCurrency($model->discount); ?></dd>
	<dt class="m-r-10">SubTotal</dt>
    <dd class = "total-horizontal-dd text-right"><?= Yii::$app->formatter->asCurrency(round($model->getSubTotal(), 2)); ?></dd>
    <dt class="m-r-10">Tax</dt>
    <dd class = "total-horizontal-dd text-right"><?= Yii::$app->formatter->asCurrency($model->tax); ?></dd>
    <dt class="m-r-10">Total</dt>
    <dd class = "total-horizontal-dd text-right"><?= Yii::$app->formatter->asCurrency(round($model->privateLesson->total, 2)); ?></dd>
    <dt class="m-r-10">Paid</dt>
    <dd class = "total-horizontal-dd text-right"><?php $lessonPaid = !empty($model->getCreditAppliedAmount($model->enrolment->id)) ? $model->getCreditAppliedAmount($model->enrolment->id) : 0; ?>
    <?= Yii::$app->formatter->asCurrency(round($lessonPaid, 2)); ?></dd>
    <dt class="m-r-10">Balance</dt>
    <dd class = "total-horizontal-dd text-right"><?=   (round($model->privateLesson->balance, 2) > 0.00 && round($model->privateLesson->balance, 2) <= 0.09) || (round($model->privateLesson->balance, 2) < 0.00 && round($model->privateLesson->balance, 2) >= -0.09)  ? Yii::$app->formatter->asCurrency(round('0.00', 2)): Yii::$app->formatter->asCurrency(round($model->privateLesson->balance, 2)) ?>
    <?php if ($model->hasInvoice()) : ?>
    <dt class="m-r-10">Invoice</dt>
    <dd class = "total-horizontal-dd text-right">
            <a id="invoice_link_lesson_panel" href="#" data-url = "<?= $model->invoice->id ?>" ><?= $model->invoice->getInvoiceNumber(); ?></a>
    </dd>
    <dt class="m-r-10">Owing</dt>
    <dd class = "total-horizontal-dd text-right">
         <?= round($model->invoice->balance,2) > 0.09 ? Yii::$app->formatter->asCurrency(round($model->invoice->balance,2)) : Yii::$app->formatter->asCurrency(round(0.00, 2)); ?>
    </dd>
    <?php endif; ?>
</dl>
<?php LteBox::end()?>
<?php Pjax::end(); ?>
<script>
  $(document).on('click', '#invoice_link_lesson_panel', function () {
        var invoiceId =$(this).attr('data-url');
        var params = $.param({
            'id': invoiceId
        });
        var url = '<?php echo Url::to(['invoice/view']); ?>?'+params;
        window.open(url, '_blank');
    });
</script>