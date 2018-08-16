<?php

use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use yii\widgets\Pjax;

?>
<?php Pjax::Begin(['id' => 'lesson-cost'])?>
<?php
LteBox::begin([
    'type' => LteConst::TYPE_DEFAULT,
    'title' => 'Cost',
    'boxTools' => '<i title="Edit" class="fa fa-pencil edit-cost"></i>',
    'withBorder' => true,
])
?>
<dl class="dl-horizontal">
	<dt>Cost/hr </dt>
	<dd><?= Yii::$app->formatter->asCurrency(round($model->teacherRate, 2)); ?></dd>
    <dt>Cost </dt>
	<dd><?= Yii::$app->formatter->asCurrency(round($model->netCost, 2)); ?></dd>
    <dt>Price </dt>
	<dd><?= Yii::$app->formatter->asCurrency(round($model->getSubTotal(), 2)); ?></dd>
    <?php $lessonProfit = $model->getSubTotal() - $model->netCost; ?> 
    <dt>Profit </dt>
	<dd><?= Yii::$app->formatter->asCurrency(round($lessonProfit, 2)); ?></dd>
</dl>
<?php LteBox::end() ?>
<?php Pjax::end();?>					