<?php
   use yii\widgets\Pjax; 
/* @var $this yii\web\View */
   /* @var $model common\models\Invoice */

   $this->title = $model->id;
   $this->params['breadcrumbs'][] = ['label' => 'Proforma-Invoices', 'url' => ['index']];
   $this->params['breadcrumbs'][] = $this->title;
   ?>
<?php
   echo $this->render('/print/_invoice-header', [
       'proformaInvoiceModel'=>$model,
       'userModel'=>$model->user,
       'locationModel'=>$model->location,
]);
   ?>
        <div class="row-fluid invoice-info m-t-10">
  <?php
echo $this->render('/receive-payment/_lesson-line-item', [
    'model' => $model,
    'lessonLineItemsDataProvider' => $lessonLineItemsDataProvider,
    'searchModel'=>$searchModel,
    'print'=>true,
]);
?>
<div class="col-xs-10">
        <div class="m-l-22"> <b>Invoices</b></div>
		<?= $this->render('/receive-payment/_invoice-line-item', [
            'model' => $model,
            'searchModel' => $searchModel,
            'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
            'print'=>true,        
            ]);?>
	</div>
</div>
    <script>
        $(document).ready(function() {
            window.print();
        });
    </script>
