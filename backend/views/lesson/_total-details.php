<?php

use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use common\models\Course;
use yii\widgets\Pjax;

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
    <dd class = "total-horizontal-dd text-right"><?= Yii::$app->formatter->asCurrency($model->grossPrice); ?></dd>
    <dt class="m-r-10">Discount</dt>
	<dd class = "total-horizontal-dd text-right"><?= Yii::$app->formatter->asCurrency($model->discount); ?></dd>
	<dt class="m-r-10">SubTotal</dt>
    <dd class = "total-horizontal-dd text-right"><?= Yii::$app->formatter->asCurrency(round($model->getSubTotal(), 2)); ?></dd>
    <dt class="m-r-10">Tax</dt>
    <dd class = "total-horizontal-dd text-right"><?= Yii::$app->formatter->asCurrency($model->tax); ?></dd>
    <dt class="m-r-10">Total</dt>
    <dd class = "total-horizontal-dd text-right"><?= Yii::$app->formatter->asCurrency(round($model->netPrice, 2)); ?></dd>
    <dt class="m-r-10">Paid</dt>
    <dd class = "total-horizontal-dd text-right"><?php $lessonPaid = !empty($model->getCreditAppliedAmount($model->enrolment->id)) ? $model->getCreditAppliedAmount($model->enrolment->id) : 0; ?>
    <?= Yii::$app->formatter->asCurrency(round($lessonPaid, 2)); ?></dd>
    <dt class="m-r-10">Balance</dt>
    <dd class = "total-horizontal-dd text-right"><?= Yii::$app->formatter->asCurrency(round($model->getOwingAmount($model->enrolment->id), 2)); ?></dd>
</dl>
<?php LteBox::end()?>
<?php Pjax::end(); ?>