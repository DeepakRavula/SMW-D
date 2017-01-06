<?php
use yii\helpers\Html;
use yii\widgets\ListView;
use common\models\User;
use common\models\Invoice;
use common\models\ItemType;

?>

Dear <?php echo Html::encode($toName) ?>,<br>
<table>
    <tr>
        <td width="15%">Code</td>
        <td width="40%">Description</td>
        <td width="10%">Quantity</td>
        <td width="10%">Price</td>
        <td width="10%">Total</td>
    </tr>
</table>
    <?= ListView::widget([
            'dataProvider' => $invoiceLineItemsDataProvider,
            'itemView' => '@console/mail/_view',
            'summary'=>'',
            'itemOptions' => ['class' => 'col-md-5'],

    ]); ?>
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
<div>
    <?php echo $model->reminderNotes; ?>
</div>
<br>
Thank you<br>
Arcadia Music Academy Team.<br>