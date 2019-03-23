<?php

use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $model common\models\PaymentMethods */
/* @var $form yii\bootstrap\ActiveForm */
?>
<div class = "row">
<?php if ($lessonLineItemsDataProvider->totalCount > 0) : ?>
<?= Html::label('Lessons', ['class' => 'admin-login']) ?>
<?= $this->render('_lesson-line-item', [
    'model' => $model,
    'isCreatePfi' => false,
    'lessonLineItemsDataProvider' => $lessonLineItemsDataProvider,
    'searchModel' => $searchModel
]);
?>
</div>
<?php endif; ?>
<div class = "row">
<?php if ($groupLessonLineItemsDataProvider->totalCount > 0) : ?>
<?= Html::label('Group Lessons', ['class' => 'admin-login']) ?>
<?= $this->render('_group-lesson-line-item', [
    'model' => $model,
    'isCreatePfi' => false,
    'lessonLineItemsDataProvider' => $groupLessonLineItemsDataProvider,
    'searchModel' => $groupLessonSearchModel
]);
?>
<?php endif; ?>
</div>
<div class = "row">
<?php if ($invoiceLineItemsDataProvider->totalCount > 0) : ?>
<?= Html::label('Invoices', ['class' => 'admin-login']) ?>
<?= $this->render('_invoice-line-item', [
    'model' => $model,
    'isCreatePfi' => false,
    'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
    'searchModel' => $searchModel
]);
?>
<?php endif; ?>
</div>
<div class = "row">
<?php if ($creditDataProvider->totalCount > 0) : ?>
<?= Html::label('Credits', ['class' => 'admin-login']) ?>
<?= $this->render('_credits-available', [
    'creditDataProvider' => $creditDataProvider,
]);
?>
</div>
<?php endif; ?> 
<table style = "width:100%;">
<table style = "width:50%">
<table class = "table table-condensed">
<tr>
<td style = "width:600px">Total</td>
<td style = "width:600px;text-align:right;"><?= $total;?></td>
</tr>
</div>
</div>
