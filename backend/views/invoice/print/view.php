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
      <div class="col-xs-12">
        <h2 class="page-header">
          <span class="logo-lg"><b>Arcadia</b>SMW</span>
          <small class="pull-right"><?= Yii::$app->formatter->asDate($model->date); ?></small>
        </h2>
      </div>
           <div class="row invoice-info">
      <div class="col-sm-4 invoice-col">
        From
        <address>
          <strong>Arcadia Music Academy ( <?= $model->location->name; ?> )</strong><br>
          <?php if (!empty($model->location->address)): ?>
              <?= $model->location->address; ?>
          <?php endif; ?>
          <br/>
          <?php if (!empty($model->location->city_id)): ?>
              <?= $model->location->city->name; ?>
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
      <!-- /.col -->
      <div class="col-sm-4 invoice-col">
        To
        <?php if(!empty($model->user)) : ?>
        <address>
          <strong><?php echo isset($model->user->publicIdentity) ? $model->user->publicIdentity : null?></strong><br>
          <?php
          $addresses = $model->user->addresses;
          foreach ($addresses as $address) {
              if ($address->label === 'Billing') {
                  $billingAddress = $address;
                  break;
              }
          }
          $phoneNumber = $model->user->phoneNumber;
          ?>
          <?php if (!empty($billingAddress->address)) : ?>
              <?= $billingAddress->address; echo '<br/>'; ?>
          <?php endif; ?>
          <?php if (!empty($billingAddress->city->name)) : ?>
              <?= $billingAddress->city->name; echo '<br/>'; ?>
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
      <div class="col-sm-4 invoice-col">
        <b><?= $model->getInvoiceNumber();?></b><br>
        <br>
        <b>Date:</b><?= Yii::$app->formatter->asDate($model->date); ?> <br>
        <b>Status:</b>  <?= $model->getStatus(); ?><br>
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
                 'format' => 'currency',
         'label' => 'Net Price',
                 'value' => function ($data) {
                     return $data->netPrice;
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
        <p class="lead">Notes:</p>
        <?php if (!empty($model->notes)):?>
        <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
         <?php echo $model->notes; ?>
        </p>
        <?php endif; ?>
      </div>
      <!-- /.col -->
      <div class="col-xs-6">
          <?php if (!empty($model->dueDate)) : 
              echo '<p class="lead">Due Date  '. Yii::$app->formatter->asDate($model->dueDate).'</p>';
           endif; ?>
          <div class="table-responsive">
          <table class="table">
            <tbody><tr>
              <th style="width:50%">Discounts:</th>
              <td><?= Yii::$app->formatter->format($model->totalDiscount, ['currency']); ?></td>
            </tr>
            <tr>
              <th>SubTotal</th>
              <td><?= Yii::$app->formatter->format($model->subTotal, ['currency']); ?></td>
            </tr>
            <tr>
              <th>Tax:</th>
              <td><?= Yii::$app->formatter->format($model->tax, ['currency']); ?></td>
            </tr>
            <tr>
              <th>Total:</th>
              <td><?= Yii::$app->formatter->format($model->total, ['currency']); ?></td>
            </tr>
            <tr>
              <th>Paid:</th>
              <td><?= Yii::$app->formatter->format($model->invoicePaymentTotal, ['currency']); ?></td>
            </tr>
            <tr>
              <th>Balance:</th>
              <td><?= Yii::$app->formatter->format($model->balance, ['currency']); ?></td>
            </tr>
          </tbody></table>
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
