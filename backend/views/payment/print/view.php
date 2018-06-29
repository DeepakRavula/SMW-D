<?php
   use yii\widgets\Pjax; 
   use common\models\Location;
/* @var $this yii\web\View */
   /* @var $model common\models\Invoice */

   $this->title = $model->id;
   $this->params['breadcrumbs'][] = ['label' => 'Proforma-Invoices', 'url' => ['index']];
   $this->params['breadcrumbs'][] = $this->title;
   ?>
<div class="row-fluid invoice-info m-t-10">
  <?php
echo $this->render('/payment/_lesson-line-item', [
    'model' => $model,
    'lessonDataProvider' => $lessonDataProvider,
]);
?>
<div class="col-xs-10">
        <div class="m-l-22"> <b>Invoices</b></div>
		<?= $this->render('/payment/_invoice-line-item', [
            'model' => $model,
            'invoiceDataProvider' => $invoiceDataProvider,        
            ]);?>
	</div>
</div>
    <script>
        $(document).ready(function() {
            window.print();
        });
    </script>
