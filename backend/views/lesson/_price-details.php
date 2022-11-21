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
    'boxTools' => $this->render('_price-action-menu', [
        'model' => $model,
    ]),
    'title' => 'Price Details',
    'withBorder' => true,
])
?>
<dl class="dl-horizontal lesson-discount">
	<dt class="m-r-10">Unit Program Price</dt>
	<dd><?= Yii::$app->formatter->asCurrency($model->programRate); ?></dd>
	<dt class="m-r-10">Gross Program Rate</dt>
    <dd><?= Yii::$app->formatter->asCurrency($model->grossPrice); ?></dd>
    <dt class="m-r-10">Net Program Rate</dt>
    <dd><?= Yii::$app->formatter->asCurrency($model->netPrice); ?></dd>
    <dt class="m-r-10">Unit Teacher Price</dt>
	<dd><?= Yii::$app->formatter->asCurrency($model->teacherRate); ?></dd>
	<dt class="m-r-10">Gross Teacher Rate</dt>
    <dd><?= Yii::$app->formatter->asCurrency($model->netCost); ?></dd>
</dl>
<?php LteBox::end()?>
<?php Pjax::end(); ?>