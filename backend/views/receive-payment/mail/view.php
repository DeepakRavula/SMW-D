<?php

use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $model common\models\PaymentMethods */
/* @var $form yii\bootstrap\ActiveForm */
?>
<?= Html::label('Lessons', ['class' => 'admin-login']) ?>
<?= $this->render('/receive-payment/_lesson-line-item', [
    'model' => $model,
    'isCreatePfi' => false,
    'lessonLineItemsDataProvider' => $lessonLineItemsDataProvider,
    'searchModel' => $searchModel
]);
?>

<?= Html::label('Group Lessons', ['class' => 'admin-login']) ?>
<?= $this->render('/receive-payment/_group-lesson-line-item', [
    'model' => $model,
    'isCreatePfi' => false,
    'lessonLineItemsDataProvider' => $groupLessonLineItemsDataProvider,
    'searchModel' => $groupLessonSearchModel
]);
?>
<?php if ($invoiceLineItemsDataProvider->totalCount > 0) : ?>
<?= Html::label('Invoices', ['class' => 'admin-login']) ?>
<?= $this->render('/receive-payment/_invoice-line-item', [
    'model' => $model,
    'isCreatePfi' => false,
    'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
    'searchModel' => $searchModel
]);
?>
<?php endif; ?>
<?php if ($creditDataProvider->totalCount > 0) : ?>
<?= Html::label('Credits', ['class' => 'admin-login']) ?>
<?= $this->render('/receive-payment/_credits-available', [
    'creditDataProvider' => $creditDataProvider,
]);
?>
<?php endif; ?> 