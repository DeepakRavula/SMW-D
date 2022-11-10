<?php

use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $model common\models\PaymentMethods */
/* @var $form yii\bootstrap\ActiveForm */
?>
<?= $this->render('/print/_invoice-header', [
       'userModel' => $user,
       'locationModel' => $user->location->location,
]);
?>
<div class = "row">
<?= Html::label('Lessons', ['class' => 'admin-login']) ?>
<?= $this->render('_lesson-line-item', [
    'model' => $model,
    'isCreatePfi' => false,
    'lessonLineItemsDataProvider' => $lessonLineItemsDataProvider,
    'searchModel' => $searchModel
]);
?>
</div>
<div class = "row">
<?= Html::label('Group Lessons', ['class' => 'admin-login']) ?>
<?= $this->render('_group-lesson-line-item', [
    'model' => $model,
    'isCreatePfi' => false,
    'lessonLineItemsDataProvider' => $groupLessonLineItemsDataProvider,
    'searchModel' => $groupLessonSearchModel
]);
?>
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

<script>
    $(document).ready(function() {
        window.print();
    });
</script>  