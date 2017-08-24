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
        <div class="row-fluid" >
            <div class="logo invoice-col" style="width: 100%">
                <img class="login-logo-img" src="<?= Yii::$app->request->baseUrl ?>/img/logo.png" />
                <div class="invoice-status">
                    <div class="invoice-col" style="width: 125px; text-align:right;">
                        <p class="invoice-number" style="font-weight:700; font-size:16px;">
                            <h3><strong><?= $model->getInvoiceNumber();?></strong></h3>
                        </p>
                        <p>
                            <?= Yii::$app->formatter->asDate($model->date); ?>
                        </p>
                        <p>
                            <?= $model->getStatus(); ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="invoice-col " style="clear: both;">
                <div class="invoice-print-address">
                    <ul>
						<li><strong>Arcadia Music Academy ( <?= $model->location->name; ?> )</strong></li>
                        <li>
                            <?php if (!empty($model->location->address)): ?>
                                <?= $model->location->address;?>
                            <?php endif; ?>
                        </li>
						<li>
                            <?php if (!empty($model->location->city_id)): ?>
                                <?= $model->location->city->name;?>
                            <?php endif; ?>
							<?php if (!empty($model->location->province_id)): ?>
                                <?= ', ' . $model->location->province->name;?>
                            <?php endif; ?>
						</li>
						<li>
							<?php if (!empty($model->location->postal_code)): ?>
                                <?= $model->location->postal_code;?>
                            <?php endif; ?>
                        </li>
                    </ul>
                    <ul>
                        <li>
                            </br>
                        </li>
                        <li>
                            <?php if (!empty($model->location->phone_number)): ?>
                                <?= $model->location->phone_number?>
                            <?php endif; ?>
                        </li>
                        <li>
                            <?php if (!empty($model->location->email)): ?>
                                <?= $model->location->email?>
                            <?php endif; ?>
                        </li>
						<li>
                           www.arcadiamusicacademy.com
                        </li>
                    </ul>
                </div>
            </div>
            <div class="invoice-col" style="clear:both; ">
				<?php if(!empty($model->user)) : ?>
                <div class="invoice-print-address">
                    <ul>
						<li>
						<strong>Customer</strong>
						</li>
                        <li>
                            <h1 class="m-0" style="font-size:14px;">
                     <?php echo isset($model->user->publicIdentity) ? $model->user->publicIdentity : null?>
                  </h1>
                        </li>
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
                        <li>
                            <!-- Billing address -->
                            <?php if (!empty($billingAddress->address)) : ?>
								<?= $billingAddress->address; ?>
							<?php endif; ?>
						</li>
						<li>                          
                            <?php if (!empty($billingAddress->city->name)) : ?>
								<?= $billingAddress->city->name; ?>
							<?php endif; ?>                          
                            <?php if (!empty($billingAddress->province->name)) : ?>
								<?= ', ' . $billingAddress->province->name; ?>
							<?php endif; ?>
						</li>
						<li>                           
                            <?php if (!empty($billingAddress->postal_code)) : ?>
								<?= $billingAddress->postal_code; ?>
							<?php endif; ?>
						</li>
                    </ul>
                    <ul>
                        <li>
                            </br>
                        </li>
						<li>
                            <!-- Phone number -->
                            <?php if (!empty($phoneNumber)) : ?>
                                <?php echo $phoneNumber->number; ?>
							<?php endif; ?>
                        </li>
                        <li>
                            <?php if (!empty($model->user->email)): ?>
                                <?php echo $model->user->email?>
                            <?php endif; ?>
                        </li>   
                    </ul>
                </div>
			<?php endif; ?>
            </div>
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
        <table class="table-responsive below-description " style="width:100%">
            <tr>
                <?php if (!empty($model->notes)):?>
                    <td class="notes-table">
                        <div class="row-fluid m-t-15 m-b-15">
                            <em><strong> Notes: </strong><Br>
               <?php echo $model->notes; ?></em>
                        </div>
                    </td>
                    <?php endif; ?>
                        <?php if (!empty($model->payments)) : ?>
                            <td class="payment-method-table p-t-10">
                                <?php yii\widgets\Pjax::begin(['id' => 'payment-index']); ?>
                                    <?php echo GridView::widget([
               'dataProvider' => $paymentsDataProvider,
               'tableOptions' => ['class' => 'table  m-0 table-more-condensed inner-payment-table'],
               'headerRowOptions' => ['class' => 'bg-light-gray'],
               'columns' => [
                   [
                       'label' => 'Payment',
                       'value' => function ($data) {
                           return $data->paymentMethod->name;
                       },
                       'headerOptions' => ['class' => 'text-left'],
                       'contentOptions' => ['class' => 'text-left'],
                   ],
               [
                       'value' => function ($data) {
                           return !empty($data->reference) ? $data->reference : null;
                       },
                   ],
                   [
                       'format' => 'currency',
                       'value' => function ($data) {
                         return $data->invoice->getInvoicePaymentMethodTotal($data->payment_method_id);
                       },
                       'headerOptions' => ['class' => 'text-left'],
                       'contentOptions' => ['class' => 'text-left', 'style' => 'width:80px;'],
                   ],
               ],
               ]); ?>
                                        <?php yii\widgets\Pjax::end(); ?>
                            </td>
                            <?php endif; ?>
                                <td rowspan="2" class="subtotal-table p-t-10">
                                    <table class="table-invoice-childtable table-more-condensed" style="float:right; width:auto;">
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
                                </td>
            </tr>
        </table>
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
