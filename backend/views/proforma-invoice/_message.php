<?php

use yii\widgets\ListView;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;

/* @var $this yii\web\View */
/* @var $model common\models\Payments */
/* @var $form yii\bootstrap\ActiveForm */
?>
<?php $boxTools = '<i title="Edit" class="fa fa-pencil add-invoice-note m-r-10"></i>';?>
<?php if (empty($model->notes)) :?>
<?php $boxTools = '<i title="Add" class="fa fa-plus add-invoice-note m-r-10"></i>';?> <?php endif;?>
<?php
LteBox::begin([
    'type' => LteConst::TYPE_DEFAULT,
    'title' => 'Message',
    'withBorder' => true,
    'boxTools' => $boxTools,
])
?>
<?= $model->notes;?>
<?php LteBox::end() ?>
