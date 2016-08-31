<?php
use yii\helpers\Html;
use yii\grid\GridView;
use backend\models\search\InvoiceSearch;
use common\models\Invoice;
use common\models\ItemType;
?>
<div class="invoice-view p-50">
         <div class="row">
		<div class="col-xs-12 p-0">
          <h2 class="m-0">
            <a href="<?= Yii::getAlias('@frontendUrl') ?>" class="logo pull-left">
                <!-- Add the class icon to your logo image or logo icon to add the margining -->                
                <img class="login-logo-img" src="<?= Yii::$app->request->baseUrl ?>/img/logo.png"  />        
            </a>
		<?= Html::a('<i class="fa fa-envelope-o"></i> Mail this Invoice', ['send-mail', 'id' => $model->id], ['class' => 'btn btn-default pull-right  m-l-20',]) ?>  
          <?= Html::a('<i class="fa fa-print"></i> Print', ['print', 'id' => $model->id], ['class' => 'btn btn-default pull-right', 'target'=>'_blank',]) ?>  
          <div class="pull-left invoice-address text-gray">
            <div class="row-fluid">
              <h2 class="m-0 text-inverse"><strong><?= (int) $model->type === InvoiceSearch::TYPE_PRO_FORMA_INVOICE ? '' : 'INVOICE'?> </strong></h2>
          </div>
          <small><?php if( ! empty($model->user->userLocation->location->address)): ?>
                <?= $model->user->userLocation->location->address?>
			<?php endif;?>
			<?php if( ! empty($model->user->userLocation->location->phone_number)): ?><br>
            <?= $model->user->userLocation->location->phone_number?>
			<?php endif;?> 
      </small> 
      </div>
      <div class="clearfix"></div>
          </h2>
        </div>
        <!-- /.col -->
      </div>
    <div class="row invoice-info m-t-20">
        <!-- /.col -->
        <div class="col-sm-8 invoice-col m-b-20 p-0">
          <div class="row m-t-10">
            <div class="col-xs-8">
              <h4 class="m-0 f-w-400"><strong><?= isset($model->user->publicIdentity) ? $model->user->publicIdentity : null?></strong></h4>
              <div class="text-gray">
	   		<?php
	            $addresses = $model->user->addresses;
                foreach($addresses as $address){
                  if($address->label === 'Billing'){
                    $billingAddress = $address;
                    break;
                  }
                }
                $phoneNumber = $model->user->phoneNumber; 
            ?>
            <!-- Billing address -->
            <?php if(! empty($billingAddress)){ ?>
              <?php 
                    echo $billingAddress->address . '<br> ' . $billingAddress->city->name . ', ';
                    echo $billingAddress->province->name . '<br>' . $billingAddress->country->name . ' ';
                    echo $billingAddress->postal_code;
               } ?>
            <div class="row-fluid m-t-20">
               <?php if( ! empty($model->user->email)): ?>
               <?= 'E: '; ?><?= $model->user->email?>
               <?php endif;?>
            </div>
            <!-- Phone number -->
            <div class="row-fluid">
              <?php if(! empty($phoneNumber)){ ?><?= 'P: '; ?>
              <?= $phoneNumber->number; } ?>
            </div>
            </div></div>
          </div>
        </div>
        <!-- /.col -->
        <div class="col-sm-4 invoice-col m-t-10 text-right p-0">
            <div class="row-fluid  text-gray">
              <div class="col-md-4 pull-right text-right p-r-0"><?= (int) $model->type === InvoiceSearch::TYPE_PRO_FORMA_INVOICE ? '' : '#' . $model->invoice_number?></div>
              <div class="col-md-2 pull-right"><?= (int) $model->type === InvoiceSearch::TYPE_PRO_FORMA_INVOICE ? '' : 'Number:'?> </div> 
              <div class="clearfix"></div>
            </div>
          <div class="row-fluid text-gray">
              <div class="col-md-4 pull-right text-right p-r-0"><?= Yii::$app->formatter->asDate($model->date);?></div>
              <div class="col-md-2 pull-right">Date:</div>
              <div class="clearfix"></div>
          </div>
          <div class="row-fluid text-gray">
			  <?php if((int) $model->type === InvoiceSearch::TYPE_INVOICE):?>
				  <div class="col-md-4 pull-right text-right p-r-0">
				  <?= $model->getStatus();?></div>
				  <div class="col-md-2 pull-right">Status:</div>
			<?php endif;?>
              <div class="clearfix"></div>
            </div>
          </div>
	<?php if($model->type === Invoice::TYPE_INVOICE):?>
	<div id="add-misc-item" class="col-md-12">
    <div class="row m-b-20">
	<a href="#" class="add-new-misc text-add-new"><i class="fa fa-plus-circle"></i> Add Misc</a>
	<div class="clearfix"></div>
  </div>
	</div>
	<?php endif;?>
    <?php yii\widgets\Pjax::begin(['id' => 'lesson-index']); ?>
        <?php echo GridView::widget([
            'dataProvider' => $invoiceLineItemsDataProvider,
            'tableOptions' =>['class' => 'table table-bordered m-0'],
            'headerRowOptions' => ['class' => 'bg-light-gray' ],
            'columns' => [
				[
					'label' => 'Code',
					'value' => function($data) {
						if((int) $data->item_type_id === ItemType::TYPE_LESSON){
							return 'LESSON';
						}else{
							return 'MISC';
						}
					}
				],
				[
					'label' => 'Description',
					'value' => function($data) {
							return $data->description;
					}
				],
                [ 
                	'attribute' => 'unit',
	               	'label' => 'Quantity',
	                'headerOptions' => ['class' => 'text-center'],
    	            'contentOptions' => ['class' => 'text-center'],
        	        'enableSorting' => false,
                ],
				[
            		'label' => 'Price',
                    'headerOptions' => ['class' => 'text-center'],
                    'contentOptions' => ['class' => 'text-center'],
            		'value' => function($data) {
						if($data->item_type_id === ItemType::TYPE_LESSON){
							return $data->lesson->enrolment->program->rate;
						}else{
							return $data->amount;
						}
					}	
            	],
                [ 
                    'attribute' => 'tax_rate',
                    'label' => 'Tax',
                    'headerOptions' => ['class' => 'text-center'],
                    'contentOptions' => ['class' => 'text-center'],
                    'enableSorting' => false,
                ],
				[ 
                    'attribute' => 'tax_status',
                    'label' => 'Tax Status',
                    'headerOptions' => ['class' => 'text-center'],
                    'contentOptions' => ['class' => 'text-center'],
                    'enableSorting' => false,
                ],
                [
	                'attribute' => 'amount',
                	'label' => 'Total',
            	    'enableSorting' => false,
					'value' => function($data) {
						if($data->item_type_id === ItemType::TYPE_LESSON){
							return $data->amount;
						}else{
							return $data->amount + $data->tax_rate;
						}
					},	
					'headerOptions' => ['class' => 'text-right'],
                    'contentOptions' => ['class' => 'text-right'],
                ],
				[
					'class'=>'yii\grid\ActionColumn',
					'template' => '{delete-line-item}',
					'buttons' => [
    					'delete-line-item' => function ($url, $model, $key) {
  					      return Html::a('<i class="fa fa-times" aria-hidden="true"></i>', ['delete-line-item', 'id'=>$model->id, 'invoiceId' => $model->invoice->id]);
    					},
					]
				]
            ],
        ]); ?>
    <?php yii\widgets\Pjax::end(); ?>

		<?php echo $this->render('_line-item') ?>
    <div class="row">
        <!-- /.col -->
        <div class="col-xs-12">
          <div class="table-responsive">
            <table class="table table-invoice-total">
              <tbody>
                <tr>
                  <td colspan="4">
                    <?php if(! empty($model->notes)):?>
                    <div class="row-fluid m-t-20">
                      <em><strong>Printed Notes: </strong><Br>
                        <?= $model->notes; ?></em>
                      </div>
                      <?php endif;?>
                      <?php if(! empty($model->notes) && ! empty($model->internal_notes)):?>
                      <hr class="right-side-faded">
                      <?php endif;?>
                      <?php if(! empty($model->internal_notes)):?>
                      <div class="row-fluid">
                      <em><strong>Internal notes:</strong><Br> <?= $model->internal_notes; ?></em>
                    </div>
                    <?php endif;?>
                  </td>
                  <td colspan="2">
                    <table class="table-invoice-childtable">
			  	<?php if((int) $model->type === InvoiceSearch::TYPE_INVOICE):?>
				    <tr>
                      <td>SubTotal</td>
                      <td><?= $model->subTotal;?></td>
                    </tr> 
                     <tr>
                      <td>Tax</td>
                      <td><?= $model->sumOfLineItemTax;?></td>
                    </tr>
					<tr>
                      <td>Paid</td>
                      <td><?= $model->invoicePaymentTotal;?></td> 
                    </tr>
				<?php endif;?>
				    <tr>
                      <tr>
                      <td><strong>Total</strong></td>
                      <td><strong><?= $model->total;?></strong></td> 
			  	<?php if((int) $model->type === InvoiceSearch::TYPE_INVOICE):?>
                    </tr>
                      <td class="p-t-20">Balance</td>
                      <td class="p-t-20"><?= $model->invoiceBalance;?></td> 
                    </tr>
				<?php endif;?>
                    </table>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <!-- /.col -->
        </div>
<div class="clearfix"></div>
<div class="row no-print">
  <div class="col-xs-12">
  </div>
</div>
</div>
</div>
<script>
$(document).ready(function() {
	$('#add-misc-item').click(function(){
		$('#invoice-line-item-modal').modal('show');
  });
});
</script>