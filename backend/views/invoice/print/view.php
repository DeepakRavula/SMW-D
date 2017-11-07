<?php
   use yii\grid\GridView;
   use common\models\InvoiceLineItem;
   use backend\models\search\InvoiceSearch;
   use common\models\ItemType;
   /* @var $this yii\web\View */
   /* @var $model common\models\Invoice */

   $this->title = $model->id;
   $this->params['breadcrumbs'][] = ['label' => 'Invoices', 'url' => ['index']];
   $this->params['breadcrumbs'][] = $this->title;
   ?>
    <div class="invoice-view">
       <div class="row">
      <div class="col-md-12">
        <h2 class="page-header">
          <span class="logo-lg"><img class="login-logo-img" src="<?= Yii::$app->request->baseUrl ?>/img/logo.png" /></span>
          <small class="pull-right"><?= Yii::$app->formatter->asDate($model->date); ?></small>
        </h2>
      </div>
       </div>
        <div class="row">
      <div class="col-md-6 invoice-col">
          <div class="invoice-print-address">
        From
        <address>
          <b>Arcadia Music Academy ( <?= $model->location->name; ?> )</b><br>
          <?php if (!empty($model->location->address)): ?>
              <?= $model->location->address; ?>
          <?php endif; ?>
          <br/>
          <?php if (!empty($model->location->city_id)): ?>
              <?= $model->location->city->name; ?>,
          <?php endif; ?>        
          <?php if (!empty($model->location->province_id)): ?>
              <?= $model->location->province->name; ?>
          <?php endif; ?>
          <br/>
          <?php if (!empty($model->location->postal_code)): ?>
              <?= $model->location->postal_code; ?>
          <?php endif; ?>
          <br/>
          <?php if (!empty($model->location->phone_number)): ?>
              <?= $model->location->phone_number ?>
          <?php endif; ?>
          <br/>
          <?php if (!empty($model->location->email)): ?>
              <?= $model->location->email ?>
          <?php endif; ?>
          <br/>
          www.arcadiamusicacademy.com
        </address>
          </div>
      </div>
      <!-- /.col -->
      <div class="col-md-4 invoice-col">
          <div class="invoice-print-address">
        To
        <?php if(!empty($model->user)) : ?>
        <address>
          <strong><?php echo isset($model->user->publicIdentity) ? $model->user->publicIdentity : null?></strong><br>
          <?php
          $addresses = $model->user->addresses;
          if(!empty($model->user->billingAddress))
          {
           $billingAddress = $model->user->billingAddress;   
          }
          
          $phoneNumber = $model->user->phoneNumber;
          ?>
          <?php if (!empty($billingAddress->address)) : ?>
              <?= $billingAddress->address; echo '<br/>'; ?>
          <?php endif; ?>
          <?php if (!empty($billingAddress->city->name)) : ?>
              <?= $billingAddress->city->name; ?>,
          <?php endif; ?>  
          <?php if (!empty($billingAddress->province->name)) : ?>
              <?= $billingAddress->province->name; echo '<br/>'; ?>
          <?php endif; ?>  
          <?php if (!empty($billingAddress->postal_code)) : ?>
              <?= $billingAddress->postal_code; echo '<br/>'; ?>
          <?php endif; ?>
          <?php if (!empty($phoneNumber)) : ?>
              <?php echo $phoneNumber->number; echo '<br/>'; ?>
          <?php endif; ?>
          <?php if (!empty($model->user->email)): ?>
              <?php echo $model->user->email; echo '<br/>'; ?>
          <?php endif; ?>
          <?php endif; ?>
        </address>
      </div>
      </div>
      <div class="col-md-2 invoice-col">
        <b><?= $model->getInvoiceNumber();?></b><br>
        <br>
        <b>Date:</b><?= Yii::$app->formatter->asDate($model->date); ?> <br>
        <b>Status:</b>  <?= $model->getStatus(); ?><br>
        <?php if (!empty($model->dueDate)) : ?>
        <b>Due Date:</b><?= Yii::$app->formatter->asDate($model->dueDate);?>
           <?php endif; ?>
        
      </div>
      <!-- /.col -->
    </div>
      <!-- /.col -->
    </div>
        <div class="row-fluid invoice-info m-t-10">
            <?php yii\widgets\Pjax::begin(['id' => 'lesson-index']); ?>
                <?php echo GridView::widget([
         'dataProvider' => $invoiceLineItemsDataProvider,
         'tableOptions' => ['class' => 'table table-bordered m-0 table-more-condensed'],
         'headerRowOptions' => ['class' => 'bg-light-gray'],
         'summary' => '',
         'columns' => [
            [
         		'label' => 'Description',
            	'headerOptions' => ['class' => 'text-left'],
            	'value' => function ($data) {
                     return $data->description;
                 },
        	],
             [
         'label' => 'Qty',
         'value' => function ($data) {
                     return $data->unit;
                 },
                 'headerOptions' => ['class' => 'text-right'],
         'contentOptions' => ['class' => 'text-right', 'style' => 'width:50px;'],
         ],
         [
                 
         'label' => 'Net Price',
                 'value' => function ($data) {
                     return $data->itemTotal;
                 },
                 'headerOptions' => ['class' => 'text-right'],
                 'contentOptions' => ['class' => 'text-right', 'style' => 'width:80px;'],
             ],
         ],
         ]); ?>
                    <?php yii\widgets\Pjax::end(); ?>
        </div>
        <div class="row">
      <!-- accepted payments column -->
      <div class="col-xs-6">
          <strong>Payments:</strong>
          <?php
          echo $this->render('_payment', [
              'model' => $model,
              'paymentsDataProvider' => $paymentsDataProvider,
          ]);

          ?>
      </div>
      <!-- /.col -->
      <div class="col-xs-6">
          <div class="table-responsive">
              <table class="table-invoice-childtable" style="float:right; width:auto;">
                  <tr>
                      <td id="invoice-discount">Discounts</td>
                      <td><?= Yii::$app->formatter->format($model->totalDiscount, ['currency']); ?></td>
                  </tr>
                  <tr>
                      <td>SubTotal</td>
                      <td>
                          <?= Yii::$app->formatter->format($model->subTotal, ['currency']); ?>
                      </td>
                  </tr>
                  <tr>
                      <td>Tax</td>
                      <td>
                          <?= Yii::$app->formatter->format($model->tax, ['currency']); ?>
                      </td>
                  </tr>
                  <tr>
                      <td><strong>Total</strong></td>
                      <td><strong><?= Yii::$app->formatter->format($model->total, ['currency']); ?></strong></td>
                  </tr>
                  <tr>
                      <td>Paid</td>
                      <td>
                          <?= Yii::$app->formatter->format($model->invoicePaymentTotal, ['currency']); ?>
                      </td>
                  </tr>
                  <tr class="last-balance">
                      <td class="p-t-0"><strong>Balance</strong></td>
                      <td class="p-t-0"><strong><?= Yii::$app->formatter->format($model->balance, ['currency']); ?></strong></td>
                  </tr>
              </table>
          </div>
      </div>
      <!-- /.col -->
        </div>
        <!-- /.col -->
        </div>
    <div class="reminder_notes text-muted well well-sm no-shadow" style="clear:both; margin-top: 20px; position: relative;">
        <?php echo $model->reminderNotes; ?>
    </div>
    <script>
        $(document).ready(function() {
            window.print();
        });
    </script>
