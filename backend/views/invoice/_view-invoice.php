<?php
use yii\helpers\Html;
use yii\grid\GridView;
use backend\models\search\InvoiceSearch;

?>
<div class="invoice-view p-50">
         <div class="row">
		<div class="col-xs-12 p-0">
          <h2 class="m-0">
            <a href="<?php echo Yii::getAlias('@frontendUrl') ?>" class="logo pull-left">
                <!-- Add the class icon to your logo image or logo icon to add the margining -->                
                <img class="login-logo-img" src="<?= Yii::$app->request->baseUrl ?>/img/logo.png"  />        
            </a>
		<?php echo Html::a('<i class="fa fa-envelope-o"></i> Mail this Invoice', ['send-mail', 'id' => $model->id], ['class' => 'btn btn-default pull-right',]) ?>  
          <?php echo Html::a('<i class="fa fa-print"></i> Print', ['print', 'id' => $model->id], ['class' => 'btn btn-default pull-right', 'target'=>'_blank',]) ?>  
          <div class="pull-left invoice-address text-gray">
            <div class="row-fluid">
              <h2 class="m-0 text-inverse"><strong><?php echo (int) $model->type === InvoiceSearch::TYPE_PRO_FORMA_INVOICE ? '' : 'INVOICE'?> </strong></h2>
          </div>
          <small><?php if( ! empty($model->lineItems[0]->lesson->enrolment->student->customer->userLocation->location->address)): ?>
                <?php echo $model->lineItems[0]->lesson->enrolment->student->customer->userLocation->location->address?>
			<?php endif;?>
			<?php if( ! empty($model->lineItems[0]->lesson->enrolment->student->customer->userLocation->location->phone_number)): ?><br>
            <?php echo $model->lineItems[0]->lesson->enrolment->student->customer->userLocation->location->phone_number?>
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
              <h4 class="m-0 f-w-400"><strong><?php echo isset($model->lineItems[0]->lesson->enrolment->student->customer->publicIdentity) ? $model->lineItems[0]->lesson->enrolment->student->customer->publicIdentity : null?></strong></h4>
              <div class="text-gray">
	    <?php
	                    $addresses = $model->lineItems[0]->lesson->enrolment->student->customer->addresses;
                foreach($addresses as $address){
                  if($address->label === 'Billing'){
                    $billingAddress = $address;
                    break;
                  }
                }
                $phoneNumber = $model->lineItems[0]->lesson->enrolment->student->customer->phoneNumber; 
            ?>
            <!-- Billing address -->
            <?php if(! empty($billingAddress)){ ?>
              <?php 
                    echo $billingAddress->address . '<br> ' . $billingAddress->city->name . ', ';
                    echo $billingAddress->province->name . '<br>' . $billingAddress->country->name . ' ';
                    echo $billingAddress->postal_code;
               } ?>
            <div class="row-fluid m-t-20">
              <?php if( ! empty($model->lineItems[0]->lesson->enrolment->student->customer->email)): ?>
              <?php echo 'E: '; ?><?php echo $model->lineItems[0]->lesson->enrolment->student->customer->email?>
              <?php endif;?>
            </div>
            <!-- Phone number -->
            <div class="row-fluid">
              <?php if(! empty($phoneNumber)){ ?><?php echo 'P: '; ?>
              <?php echo $phoneNumber->number; } ?>
            </div>
            </div></div>
          </div>
        </div>
        <!-- /.col -->
        <div class="col-sm-4 invoice-col m-t-10 text-right p-0">
            <div class="row-fluid  text-gray">
              <div class="col-md-4 pull-right text-right p-r-0"><?php echo (int) $model->type === InvoiceSearch::TYPE_PRO_FORMA_INVOICE ? '' : '#' . $model->invoice_number?></div>
              <div class="col-md-2 pull-right"><?php echo (int) $model->type === InvoiceSearch::TYPE_PRO_FORMA_INVOICE ? '' : 'Number:'?> </div> 
              <div class="clearfix"></div>
            </div>
          <div class="row-fluid text-gray">
              <div class="col-md-4 pull-right text-right p-r-0"><?php echo date("d/m/Y", strtotime($model->date));?></div>
              <div class="col-md-2 pull-right">Date:</div>
              <div class="clearfix"></div>
          </div>
          <div class="row-fluid text-gray">
              <div class="col-md-4 pull-right text-right p-r-0"><?php echo $model->status($model);?></div>
              <div class="col-md-2 pull-right">Status:</div>
              <div class="clearfix"></div>
            </div>
          </div>
    <?php yii\widgets\Pjax::begin(['id' => 'lesson-index']); ?>
        <?php echo GridView::widget([
            'dataProvider' => $invoiceLineItemsDataProvider,
            'tableOptions' =>['class' => 'table table-bordered m-0'],
            'headerRowOptions' => ['class' => 'bg-light-gray' ],
            'columns' => [
				[
					'label' => 'Student Name',
					'value' => function($data) {
						return !empty($data->lesson->enrolment->student->fullName) ? $data->lesson->enrolment->student->fullName : null;
					},
				],
				[
					'label' => 'Program Name',
					'value' => function($data) {
						return !empty($data->lesson->enrolment->program->name) ? $data->lesson->enrolment->program->name : null;
					},
				],
                [ 
                	'attribute' => 'unit',
	               	'label' => 'Unit',
	                'headerOptions' => ['class' => 'text-center'],
    	            'contentOptions' => ['class' => 'text-center'],
        	        'enableSorting' => false,
                ],
				[
            		'label' => 'Rate/hr',
                    'headerOptions' => ['class' => 'text-center'],
                    'contentOptions' => ['class' => 'text-center'],
            		'value' => function($data) {
            			return !empty($data->lesson->enrolment->program->rate) ? $data->lesson->enrolment->program->rate : null;
            			},
            	],
                [ 
            	    'attribute' => 'amount',
                	'headerOptions' => ['class' => 'text-right'],
	                'contentOptions' => ['class' => 'text-right'],
    	            'label' => 'Amount',
        	        'enableSorting' => false,
                ],
                [
	                'attribute' => 'amount',
                	'label' => 'Total',
            	    'enableSorting' => false,
                ]
            ],
        ]); ?>
    <?php yii\widgets\Pjax::end(); ?>
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
                        <?php echo $model->notes; ?></em>
                      </div>
                      <?php endif;?>
                      <?php if(! empty($model->notes) && ! empty($model->internal_notes)):?>
                      <hr class="right-side-faded">
                      <?php endif;?>
                      <?php if(! empty($model->internal_notes)):?>
                      <div class="row-fluid">
                      <em><strong>Internal notes:</strong><Br> <?php echo $model->internal_notes; ?></em>
                    </div>
                    <?php endif;?>
                  </td>
                  <td colspan="2">
                    <table class="table-invoice-childtable">
					           <tr>
                      <td>SubTotal</td>
                      <td><?php echo $model->subTotal;?></td>
                    </tr> 
                     <tr>
                      <td>Tax</td>
                      <td><?php echo $model->tax;?></td>
                    </tr>
					           <tr>
                      <td>Paid</td>
                      <td><?php echo '0.00';?></td> 
                    </tr>
					           <tr>
                      <tr>
                      <td><strong>Total</strong></td>
                      <td><strong><?php echo $model->total;?></strong></td> 
                    </tr>
                      <td class="p-t-20">Balance</td>
                      <td class="p-t-20"><?php echo $model->total;?></td> 
                    </tr>
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
