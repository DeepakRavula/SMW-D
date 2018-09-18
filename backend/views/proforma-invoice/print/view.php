<?php
   use yii\widgets\Pjax; 
/* @var $this yii\web\View */
   /* @var $model common\models\Invoice */

   $this->title = $model->id;
   $this->params['breadcrumbs'][] = ['label' => 'Proforma-Invoices', 'url' => ['index']];
   $this->params['breadcrumbs'][] = $this->title;
   ?>
<?=
    $this->render('/print/_invoice-header', [
       'proformaInvoiceModel'=>$model,
       'userModel'=>$model->user,
       'locationModel'=>$model->location,
]);
   ?>
<div class="row-fluid invoice-info m-t-10">
    <?php $lessonCount = $lessonLineItemsDataProvider->getCount(); ?>
        <?php if ($lessonCount > 0) : ?>
        <div class="col-xs-10">
        <div class="m-l-22"> <b>Lessons</b></div>
<?=
    $this->render('/receive-payment/_lesson-line-item', [
        'model' => $model,
        'lessonLineItemsDataProvider' => $lessonLineItemsDataProvider,
        'searchModel'=>$searchModel,
        'print'=>true,
]);
?>
        <div>
        <?php endif; ?>
    <?php $invoiceCount = $invoiceLineItemsDataProvider->getCount(); ?>
        <?php if ($invoiceCount > 0) : ?>
        <div class="col-xs-10">
        <div class="m-l-22"> <b>Invoices</b></div>
<?= 
    $this->render('/receive-payment/_invoice-line-item', [
        'model' => $model,
        'searchModel' => $searchModel,
        'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
        'print'=>true,        
]);?>
	    </div>
        <?php endif; ?>
</div>
<div class="col-md-3 pull-right">
    <?= $this->render('/proforma-invoice/_view-bottom-summary', [
                'model' => $model,
            ]); ?>
</div>
<div style="clear:both; margin-top: 20px; position: relative;">
        <?php if (!empty($model->notes)):?>
        <strong> Notes: </strong><?php echo $model->notes; ?>
		<?php endif;?>
    </div>
<script>
    $(document).ready(function() {
        window.print();
    });
</script>
