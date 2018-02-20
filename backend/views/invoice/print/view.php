<?php
   use yii\grid\GridView;
   use yii\widgets\Pjax; 
/* @var $this yii\web\View */
   /* @var $model common\models\Invoice */

   $this->title = $model->id;
   $this->params['breadcrumbs'][] = ['label' => 'Invoices', 'url' => ['index']];
   $this->params['breadcrumbs'][] = $this->title;
   ?>
<style>
    @media print {
  .dl-invoice-summary dt {
    text-align: left;
    line-height:1.0;
    font-weight:normal;
  }
  .dl-invoice-summary dd{
	text-align: right;
        line-height:1.0;
  }
  .m-l-30
  {
      margin-left:30px;
  }
}
    </style>
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
            <div class="row">  
                <div class="m-l-30"><b>Payments</b></div>
	<div class="col-xs-9">          
		<?= $this->render('/invoice/payment/_payment-list', [
            'model' => $model,
            'invoicePaymentsDataProvider' => $invoicePaymentsDataProvider,
            'print'=>true,        
            ]);?>
	</div>
	<?php Pjax::Begin(['id' => 'invoice-bottom-summary', 'timeout' => 6000]); ?>
	<div class="col-xs-3">
		<?=
        $this->render('/invoice/_bottom-summary-list', [
            'model' => $model,
        ]);
        ?>	
	</div>
    <?php Pjax::end(); ?>
</div>
        </div>
	 <div style="clear:both; margin-top: 20px; position: relative;">
        <strong>Printed Notes: </strong><?php echo $model->notes; ?>
    </div>
    <div class="reminder_notes text-muted well well-sm no-shadow" style="clear:both; margin-top: 20px; position: relative;">
        <?php echo $model->reminderNotes; ?>
    </div>
    <script>
        $(document).ready(function() {
            window.print();
        });
    </script>
