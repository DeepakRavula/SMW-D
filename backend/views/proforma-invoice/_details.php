<?php

use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use yii\widgets\Pjax;
use yii\helpers\Html;

?>
<?php Pjax::Begin(['id' => 'invoice-details', 'timeout' => 6000]); ?>
	<?php
LteBox::begin([
    'type' => LteConst::TYPE_DEFAULT,
    'title' => 'Details',
	'withBorder' => true,
	'boxTools' => '',
])
?>

<dl class="dl-horizontal">
	<dt>Date</dt>
	<dd><?= Yii::$app->formatter->asDate($model->date); ?></dd>
</dl>
<?php LteBox::end()?>
<?php Pjax::end(); ?>