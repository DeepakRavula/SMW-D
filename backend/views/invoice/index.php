<?php

use common\models\Invoice;
use backend\models\search\InvoiceSearch;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\bootstrap\Tabs;

$this->title = 'Invoices';
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
	.smw-search{
		left: 170px;
	}
</style>

<div class="tabbable-panel">
     <div class="tabbable-line">
            <div class="smw-search">
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

   <?php 
    $indexInvoice =  $this->render('_index-invoice', [    
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
    ]);
    ?> 
    
<?php echo Tabs::widget([
    'items' => [
		[
            'label' => 'Invoice',
            'content' => (int) $searchModel->type === Invoice::TYPE_INVOICE  ? $indexInvoice : null,
			'url'=>['/invoice/index','InvoiceSearch[type]' => Invoice::TYPE_INVOICE],
			'active' => (int) $searchModel->type === Invoice::TYPE_INVOICE ,
        ],
		[
            'label' => 'Pro-forma Invoice',
            'content' => (int) $searchModel->type === Invoice::TYPE_PRO_FORMA_INVOICE  ? $indexInvoice : null,
			'url'=>['/invoice/index','InvoiceSearch[type]' => Invoice::TYPE_PRO_FORMA_INVOICE ],
			'active' => (int) $searchModel->type === Invoice::TYPE_PRO_FORMA_INVOICE ,
        ],
    ],
]);?>
         
<?php ActiveForm::end(); ?>   
<div class="clearfix"></div>
</div>
</div>
