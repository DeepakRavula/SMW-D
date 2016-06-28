<?php



/* @var $this yii\web\View */
/* @var $model common\models\Invoice */

$this->title = 'Add Invoice';
$this->params['breadcrumbs'][] = ['label' => 'Invoices', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invoice-create p-20">

    <?php echo $this->render('_form', [
        'model' => $model,
		'unInvoicedLessonsDataProvider' => $unInvoicedLessonsDataProvider,
    ]) ?>

</div>
