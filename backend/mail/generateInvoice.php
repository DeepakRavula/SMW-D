<?php
use yii\helpers\Html;
use yii\grid\GridView;
use common\models\User;
use common\models\Invoice;

?>

Dear <?php echo Html::encode($toName) ?>,<br>
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
    </div>
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