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
    'title' => 'Total',
    'withBorder' => true,
])
?>
<dl class="dl-horizontal lesson-discount">
	<dt class="m-r-10">Lesson Rate/hr</dt>
	<dd><?= Yii::$app->formatter->asCurrency($model->programRate); ?></dd>
	<dt class="m-r-10">Qty</dt>
    <dd><?= $model->unit; ?></dd>
    <dt class="m-r-10">Lesson Price</dt>
    <dd><?= Yii::$app->formatter->asCurrency($model->grossPrice); ?></dd>
    <dt class="m-r-10">Discount</dt>
	<dd><?= Yii::$app->formatter->asCurrency($model->discount); ?></dd>
	<dt class="m-r-10">SubTotal</dt>
    <dd><?= Yii::$app->formatter->asCurrency($model->getSubTotal()); ?></dd>
    <dt class="m-r-10">Tax</dt>
    <dd><?= Yii::$app->formatter->asCurrency($model->tax); ?></dd>
    <dt class="m-r-10">Total</dt>
    <dd><?= Yii::$app->formatter->asCurrency($model->getTotal()); ?></dd>
    <dt class="m-r-10">Paid</dt>
    <dd><?= Yii::$app->formatter->asCurrency($model->getPaidAmount($model->id)); ?></dd>
    <dt class="m-r-10">Balance</dt>
    <dd><?= Yii::$app->formatter->asCurrency($model->getBalance($model->enrolment->id)); ?></dd>
</dl>
<?php LteBox::end()?>
<?php Pjax::end(); ?>