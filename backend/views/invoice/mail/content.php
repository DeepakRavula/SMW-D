<?php
use yii\grid\GridView;
use common\models\ItemType;
use common\models\TextTemplate;

?>

Dear Customer,<br>
	<?= $emailTemplate->header ?? 'Please find the invoice below:'; ?><Br>
            <?php yii\widgets\Pjax::begin(['id' => 'lesson-index']);
            $columns = [
        [   'label' => 'Description',
            'contentOptions' => ['style' => 'width:250px;'],
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
        ]
    ];
    if ($model->isProformaInvoice()) {
        $columns[] = [
            'label' => 'Payment',
            'format' => 'currency',
            'value' => function ($data) {
                if (!$data->isGroupLesson()) {
                    $amount = $data->proFormaLesson->getCreditAppliedAmount($data->proFormaLesson->enrolment->id) ?? 0;
                } else {
                    $amount = $data->enrolment->payment ?? 0;
                }
                return Yii::$app->formatter->asDecimal($amount);
            },
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right', 'style' => 'width:50px;'],
        ];
    }
    $columns[] = [
            'label' => 'Price',
            'format' => 'currency',
            'value' => function ($data) {
                return Yii::$app->formatter->asDecimal($data->itemTotal);
            },
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right', 'style' => 'width:50px;'],
        ];?>
        <?php yii\widgets\Pjax::begin(['id' => 'lesson-index']); ?>
        <?php echo GridView::widget([
            'dataProvider' => $invoiceLineItemsDataProvider,
            'tableOptions' => ['class' => 'table table-bordered m-0', 'style'=>'width:100%; text-align:left'],
            'headerRowOptions' => ['class' => 'bg-light-gray'],
            'summary' => false,
            'emptyText' => false,
            'columns' => $columns,
        ]); ?>
    <?php yii\widgets\Pjax::end(); ?>
    <div class="row">
        <!-- /.col -->
          <div class="table-responsive">
            <table class="table table-invoice-total" style="width: 100%;">
              <tbody>
                <tr>
                  <td colspan="4" style="width: 75%;">
                    <?php if (!empty($model->notes)):?>
                    <div class="row-fluid m-t-20">
                      <em><strong> Notes: </strong><Br>
                        <?php echo $model->notes; ?></em>
                      </div>
                      <?php endif; ?>
                  </td>
                  <td colspan="4">
                    <table class="table-invoice-childtable" style="width: 100%; float: right; text-align: left;">
                     <tr>
                      <td style="width: 50%;">SubTotal</td>
						<td><?= Yii::$app->formatter->format(
            $model->subTotal,
                            ['currency', 'USD', [
                        \NumberFormatter::MIN_FRACTION_DIGITS => 2,
                        \NumberFormatter::MAX_FRACTION_DIGITS => 2,
                    ]]
        ); ?></td>
                    </tr> 
                     <tr>
                      <td>Tax</td>
						<td>
					  <?= Yii::$app->formatter->format(
                        $model->tax,
                            ['currency', 'USD', [
                        \NumberFormatter::MIN_FRACTION_DIGITS => 2,
                        \NumberFormatter::MAX_FRACTION_DIGITS => 2,
                    ]]
                    ); ?>
					</td>
                    </tr>
                    
                      <tr>
                      <td><strong>Total</strong></td>
						<td><strong>
					<?= Yii::$app->formatter->format(
                        $model->total,
                            ['currency', 'USD', [
                        \NumberFormatter::MIN_FRACTION_DIGITS => 2,
                        \NumberFormatter::MAX_FRACTION_DIGITS => 2,
                    ]]
                    ); ?>
					</strong></td>
                    </tr>
					 <tr>
                      <td>Paid</td>
						<td> <?= Yii::$app->formatter->format(
                        $model->invoicePaymentTotal,
                            ['currency', 'USD', [
                        \NumberFormatter::MIN_FRACTION_DIGITS => 2,
                        \NumberFormatter::MAX_FRACTION_DIGITS => 2,
                    ]]
                    ); ?></td>
                    </tr>
                      <tr>
                      <td class="p-t-20">Balance</td>
						<td class="p-t-20"><strong>
					<?= Yii::$app->formatter->format(
                        $model->invoiceBalance,
                            ['currency', 'USD', [
                        \NumberFormatter::MIN_FRACTION_DIGITS => 2,
                        \NumberFormatter::MAX_FRACTION_DIGITS => 2,
                    ]]
                    ); ?>
					</strong></td>
                    </tr>
                    </table>
                  </td>
                </tr>
              </tbody>
            </table>
              Payments:
                     <?php
$columns = [
    [
        'label' => 'Date',
        'value' => function ($data) {
            return Yii::$app->formatter->asDate($data->date);
        },
        ],
    'paymentMethod.name',
    [
        'label' => 'Number',
        'value' => function ($data) {
            return $data->reference;
        },
        ],
        [
            'label' =>'Amount',
            'format' => 'currency',
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right'],
            'value' => function ($data) {
                return Yii::$app->formatter->asDecimal($data->amount);
            },
        ],
    ]; ?>

<div>
	<?php yii\widgets\Pjax::begin([
        'id' => 'invoice-payment-listing',
        'timeout' => 6000,
    ]) ?>
	<?= GridView::widget([
        'id' => 'payment-grid',
        'dataProvider' => $invoicePaymentsDataProvider,
        'columns' => $columns,
    'summary' => false,
        'emptyText' => false,
        'options' => ['class' => 'col-md-12'],
    'tableOptions' => ['class' => 'table table-condensed'],
    'headerRowOptions' => ['class' => 'bg-light-gray'],
    ]);
    ?>
<?php \yii\widgets\Pjax::end(); ?>
</div>
          </div>
        <!-- /.col -->
        </div>
<div>
    <?php echo $model->reminderNotes; ?>
</div>
<br>
<?= $emailTemplate->footer ?? 'Thank you 
Arcadia Academy of Music Team.'; ?>
