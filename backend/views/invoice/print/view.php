<?php
   use yii\widgets\Pjax; 
/* @var $this yii\web\View */
   /* @var $model common\models\Invoice */

   $this->title = $model->id;
   $this->params['breadcrumbs'][] = ['label' => 'Invoices', 'url' => ['index']];
   $this->params['breadcrumbs'][] = $this->title;
   ?>
<?php
   echo $this->render('/print/_invoice-header', [
       'invoiceModel'=>$model,
       'userModel'=>$model->user,
       'locationModel'=>$model->location,
]);
   ?>
        <div class="row-fluid invoice-info m-t-10">
  <?php
echo $this->render('/invoice/_view-line-item', [
    'model' => $model,
    'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
    'searchModel'=>$searchModel,
    'print'=>true,
]);
?>
<div class="col-xs-10">
    <?php if($model->hasPayments()) :?>
        <div class="m-l-22"> <b>Payments</b></div>
		<?= $this->render('/invoice/payment/_payment-list', [
            'model' => $model,
            'searchModel' => $searchModel,
            'invoicePaymentsDataProvider' => $invoicePaymentsDataProvider,
            'print'=>true,        
            ]);?>
	<?php endif; ?>
	</div>
	<?php Pjax::Begin(['id' => 'invoice-bottom-summary', 'timeout' => 6000]); ?>
	<div class="col-xs-2">
		<?=$this->render('/invoice/_bottom-summary-list', [
            'model' => $model,
        ]);
        ?>	
	</div>
    <?php Pjax::end(); ?>
</div>
   
	 <div style="clear:both; margin-top: 20px; position: relative;">
        <?php if (!empty($model->notes)):?>
        <strong> Notes: </strong><?php echo $model->notes; ?>
		<?php endif;?>
    </div>
    <div class="reminder_notes text-muted well well-sm no-shadow" style="clear:both; margin-top: 20px; position: relative;">
        <?php echo $model->reminderNotes; ?>
    </div>
    <script>
        $(document).ready(function() {
            window.print();
        });
    </script>
