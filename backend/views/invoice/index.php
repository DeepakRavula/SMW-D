<?php

use common\models\Invoice;
use backend\models\search\InvoiceSearch;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\bootstrap\Tabs;

$this->title = 'Invoices';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php 
 switch ($searchModel->type) {
    case Invoice::TYPE_PRO_FORMA_INVOICE:
    $invoiceTypeClassName = 'pro-forma-invoice';
    break;
    case Invoice::TYPE_INVOICE:
    $invoiceTypeClassName = 'invoice';
    break;
 }
 ?>

            <div class="search-<?= $invoiceTypeClassName; ?>">
    <i class="fa fa-search m-l-20 m-t-5 pull-left m-r-10 f-s-16"></i>
    <?php
    $form = ActiveForm::begin([
                'action' => ['index'],
                'method' => 'get',
                'options' => ['class' => 'pull-left'],
    ]);
    ?>    
    <?=
    $form->field($searchModel, 'query', [
        'inputOptions' => [
            'placeholder' => 'Search ...',
            'class' => 'search-field',
        ],
    ])->input('search')->label(false);
    ?>
    </div>
	<?php if((int) $searchModel->type === Invoice::TYPE_INVOICE) : ?>
	<div class="bulk-invoice">
		<?= Html::a('<i class="fa fa-dollar"></i> Invoice All Completed Lessons', ['all-completed-lessons'], ['class' => 'btn btn-default  m-l-20']) ?>
    </div>
<?php endif; ?>
   <?php 
    echo $this->render('_index-invoice', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
    ]);
    ?> 
<?php ActiveForm::end(); ?>   
<div class="clearfix"></div>
