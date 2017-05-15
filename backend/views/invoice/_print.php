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
    <style>
        table>thead>tr>th:first-child,
        table>tbody>tr>td:first-child {
            text-align: left !important;
        }
        
        .table-invoice-childtable>tbody>tr>td:last-of-type {
            text-align: right;
        }
        
        .table-invoice-childtable>tbody>tr>td:first-of-type {
            width: 110px;
            text-align: left !important;
        }
        
        .invoice-view .logo {
            margin-bottom: 10px;
        }
        
        .invoice-view .logo>img {
            width: 135px;
            clear: both;
        }
        
        .invoice-view .invoice-status {
            float: right;
        }
        
        .invoice-view .invoice-status .invoice-number {
            font-size: 16px;
            font-weight: bold;
            margin: 0;
        }
        
        .invoice-view .invoice-status b {
            margin: 0;
        }
        
        .invoice-view .login-logo-img {
            float: left;
            display: block;
        }
        
        .invoice-print-address {
            width: 700px;
            margin-top: 10px;
        }
        
        .invoice-print-address ul {
            display: block;
            float: left;
            width: 300px;
        }
        
        .invoice-print-address h1 {
            margin: 0;
            padding: 0;
            text-transform: capitalize;
        }
        
        .invoice-print-address ul li {
            font-size: 14px;
            font-weight: 300;
            color: #000;
        }
        
        .badge {
            border-radius: 50px;
            font-size: 18px;
            font-weight: 400;
            padding: 7px 30px;
            background: #ea212c;
        }
        
        .invoice-status p {
            padding: 0;
            margin: 0;
        }
        
        @media print {
            .invoice-view .logo>img {
                padding: 0;
                position: relative;
                left: -2px;
            }
            .invoice-print-address {
                width: 700px;
                margin-top: 10px;
            }
            .invoice-print-address ul {
                display: block;
                float: left;
                width: 300px;
            }
            .invoice-print-address h1 {
                margin: 0;
                padding: 0;
                text-transform: capitalize;
            }
            .invoice-print-address ul li {
                font-size: 14px;
                font-weight: 300;
                color: #000;
            }
            .invoice-info {
                margin-top: 15px;
            }
            .text-gray {
                color: gray !important;
            }
            .invoice-labels {
                width: 82px;
            }
            .text-left {
                text-align: left !important;
            }
            .reminder_notes {
                position: fixed;
                bottom: 0;
            }
            .notes-table {
                width: 90vw;
            }
            .notes-table,
            .payment-method-table {
                vertical-align: top;
            }
            .subtotal-table {
                float: right;
            }
            .below-description {
                width: 100%;
            }
            .table-invoice-childtable {
                width: 10vw;
                float: right !important;
            }
            .table-invoice-childtable>thead>tr>th:last-child,
            .table-invoice-childtable>tbody>tr>td:last-child {
                white-space: nowrap;
            }
            .table-invoice-childtable>tbody>tr>td:first-of-type {
                width: 110px;
            }
            .last-balance {
                border-top: 1px solid #eaeaea;
            }
            .border-bottom-gray {
                border-bottom: 1px solid #efefef;
            }
            .invoice-number {
                font-weight: bold;
            }
            .invoice-status p {
                padding: 0;
                margin: 0;
            }
        }
        
        .invoice-print-address ul li {
            font-size: 14px;
            font-weight: 300;
            color: #000;
        }
    </style>
    <div class="invoice-view">
        <div class="row-fluid" style="overflow: auto;">
            <div class="logo invoice-col" style="width: 100%">
                <img class="login-logo-img" src="<?= Yii::$app->request->baseUrl ?>/img/logo.png" />
                <div class="invoice-status">
                    <div class="invoice-col" style="width: 125px; text-align:right;">
                        <p class="invoice-number" style="font-weight:700; font-size:16px;">
                            <?= (int) $model->type === InvoiceSearch::TYPE_PRO_FORMA_INVOICE ? '' : ''?>
                                <?= (int) $model->type === InvoiceSearch::TYPE_PRO_FORMA_INVOICE ? '' : '#'.$model->invoice_number?>
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
                        <li>
                            <h1 class="m-0 text-inverse" style="font-size:14px; font-weight:600;">
                     <?= (int) $model->type === InvoiceSearch::TYPE_PRO_FORMA_INVOICE ? '' : 'Invoice'?> 
                  </h1>
                        </li>
                        <li>
                            <?php if (!empty($model->user->userLocation->location->address)): ?>
                                <?= $model->user->userLocation->location->address?>
                                    <br>
                                    <?php endif; ?>
                        </li>
                    </ul>
                    <ul>
                        <li>
                            </br>
                        </li>
                        <li>
                            <?php if (!empty($model->user->userLocation->location->phone_number)): ?>
                                <?= $model->user->userLocation->location->phone_number?>
                                    <?php endif; ?>
                        </li>
                        <li>
                            <?php if (!empty($model->user->userLocation->location->email)): ?>
                                <?= $model->user->userLocation->location->email?>
                                    <?php endif; ?>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="invoice-col" style="clear:both; ">
                <div class="invoice-print-address">
                    <ul>
                        <li>
                            <h1 class="m-0" style="font-size:14px; font-weight:600;">To
                     <?php echo isset($model->user->publicIdentity) ? $model->user->publicIdentity : null?>
                  </h1>
                        </li>
                        <li>
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
                                <!-- Billing address -->
                                <?php if (!empty($billingAddress)) {
                     ?>
                                    <?php 
                     echo $billingAddress->address.'<br> '.$billingAddress->city->name.', ';
                     echo $billingAddress->province->name.'<br>'.$billingAddress->country->name.' ';
                     echo $billingAddress->postal_code;
                     } ?>address
                        </li>
                    </ul>
                    <ul>
                        <li>
                            </br>
                        </li>
                        <li>
                            <?php if (!empty($model->user->email)): ?>
                                <?php echo 'E: '; ?>
                                    <?php echo $model->user->email?>
                                        <?php endif; ?>
                                            544-75-769
                        </li>
                        <li>
                            <!-- Phone number -->
                            <?php if (!empty($phoneNumber)) {
                     ?>
                                <?php echo 'P: '; ?>
                                    <?php echo $phoneNumber->number;
                     } ?>
                                        544-75-768
                        </li>
                    </ul>
                </div>
            <!-- Phone number -->
            <div class="row-fluid text-gray">
              <?php if (!empty($phoneNumber)) {
                    ?><?php echo 'P: '; ?>
              <?php echo $phoneNumber->number;
                } ?>
            </div>
              </address>
            </div>
            <div class="invoice-col"  style="width: 125px;">
              <b><?= (int) $model->type === InvoiceSearch::TYPE_PRO_FORMA_INVOICE ? '' : 'Invoice No.:'?></b> <?= (int) $model->type === InvoiceSearch::TYPE_PRO_FORMA_INVOICE ? '' : '#'.$model->invoice_number?><br>
              <b>Date:</b> <?= Yii::$app->formatter->asDate($model->date); ?><br>
              <?php if (!$model->isInvoice()) : ?>
              <b>Due Date:</b> <?= Yii::$app->formatter->asDate($model->dueDate); ?><br>
              <?php endif; ?>
              <b>Status:</b> <?= $model->getStatus(); ?>
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
                 'format' => 'raw',
                 'value' => function ($data) {
                     if (!empty($data->discount)) {
                         if ((int) $data->discountType === (int) InvoiceLineItem::DISCOUNT_FLAT) {
                             $discount = Yii::$app->formatter->format($data->discount, ['currency']);
                             $discountDiscription = ' (Discount - ' . $discount . ')' ;
                             $discription = $data->description . "<i>" .
                                 $discountDiscription . "</i>";
                         } else {
                             $discount = $data->discount . '%';
                             $discountDiscription = ' (Discount - ' . $discount . ')' ;
                             $discription = $data->description . "<i>" .
                                 $discountDiscription . "</i>";
                         }
                     } else {
                         $discription = $data->description;
                     }
                     return $discription;
                 },
                 'headerOptions' => ['class' => 'text-left'],
                 'contentOptions' => ['class' => 'text-left'],
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
