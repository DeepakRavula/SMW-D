<?php
use yii\helpers\Html;
use yii\grid\GridView;
use common\models\User;
use common\models\Invoice;
use common\models\ItemType;

?>

Dear <?php echo Html::encode($toName) ?>,<br>
        <?php yii\widgets\Pjax::begin(['id' => 'lesson-index']); ?>
        <?php echo GridView::widget([
            'dataProvider' => $invoiceLineItemsDataProvider,
            'tableOptions' =>['class' => 'table table-bordered m-0'],
            'headerRowOptions' => ['class' => 'bg-light-gray' ],
			'summary' => '',
            'columns' => [
				[
					'label' => 'Code',
					'value' => function($data) {
						if((int) $data->item_type_id === ItemType::TYPE_PRIVATE_LESSON){
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
						if($data->item_type_id === ItemType::TYPE_PRIVATE_LESSON){
							return $data->lesson->enrolment->program->rate;
						}else{
							return $data->amount;
						}
					}	
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
          <div class="table-responsive">
            <table class="table table-invoice-total">
              <tbody>
                <tr>
                  <td colspan="4">
                    <?php if(! empty($model->notes)):?>
                    <div class="row-fluid m-t-20">
                      <em><strong> Printed Notes: </strong><Br>
                        <?php echo $model->notes; ?></em>
                      </div>
                      <?php endif;?>
                      <?php if(! empty($model->notes) && ! empty($model->internal_notes)):?>
                      <hr class="right-side-faded">
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
        <!-- /.col -->
        </div>
</div>
<br>
Thank you<br>
Arcadia Music Academy Team.<br>