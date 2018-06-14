<?php

use yii\grid\GridView;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<?php
LteBox::begin([
    'type' => LteConst::TYPE_DEFAULT,
    'title' => 'History',
    'withBorder' => true,
])
?>
<div class="student-index"> 
    <?php yii\widgets\Pjax::begin([
        'id' => 'invoice-log',
        'timeout' => 6000,
    ]) ?>

    <?php \yii\widgets\Pjax::end(); ?>
</div>
<?php LteBox::end() ?>
	