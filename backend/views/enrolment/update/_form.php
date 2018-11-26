<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\PaymentFrequency;
use yii\helpers\Html;
use kartik\select2\Select2;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\grid\GridView;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

?>

    <?php $form = ActiveForm::begin([
        'id' => 'modal-form',
        'action' => Url::to(['enrolment/edit', 'id' => $model->id]),
    ]); ?>

    <div class="row">
        <?php if ($model->course->isPrivate()) : ?>
        <div class="col-md-8">
            <?= $form->field($model, 'paymentFrequencyId')->widget(Select2::classname(), [
                'data' => ArrayHelper::map(PaymentFrequency::find()->all(), 'id', 'name')]); 
            ?>
        </div>
        <?php endif; ?>
        <div class="col-md-6">
            <?= $form->field($paymentFrequencyDiscount, 'discount')->textInput()
                ->label('Payment Frequency Discount'); 
            ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($multipleEnrolmentDiscount, 'discount')->textInput()
                ->label('Multiple Enrolment Discount'); 
            ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
    
<?php
    $columns = [
        [
            'label' => 'Objects',
            'attribute' => 'objects',
            'headerOptions' => ['class' => 'kv-sticky-column bg-light-gray'],
            'contentOptions' => ['class' => 'kv-sticky-column'],
        ],
        [
            'label' => 'Action',
            'attribute' => 'action',
            'headerOptions' => ['class' => 'kv-sticky-column bg-light-gray'],
            'contentOptions' => ['class' => 'kv-sticky-column'],
        ],
        [
            'label' => 'Date Range',
            'attribute' => 'date_range',
            'headerOptions' => ['class' => 'kv-sticky-column bg-light-gray'],
            'contentOptions' => ['class' => 'kv-sticky-column'],
        ]
    ];
?>

<div class="preview">
    <label>Enrolment Edit Preview</label>
    <div class="row">
        <div class="col-lg-12">
            <?= GridView::widget([
                'options' => ['id' => 'enrolment-edit-preview'],
                'dataProvider' => $previewDataProvider,
                'columns' => $columns,
                'rowOptions' => function ($model, $key, $index, $grid) {
                    return ['class' => $model['class']];
                },
                'summary' => false,
                'emptyText' => false
            ]); ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Edit</h4>');
        $('#modal-popup-warning-notification').html('You have entered a \n\
                    non-approved Arcadia discount. All non-approved discounts \n\
                    must be submitted in writing and approved by Head Office \n\
                    prior to entering a discount, otherwise you are in breach \n\
                    of your agreement.').fadeIn();
        $('.modal-save').show();
        $('.modal-save').text('Confirm');
        $('#popup-modal .modal-dialog').css({'width': '600px'});
        $('.preview').hide();
    });

    $(document).on('change', '#enrolment-paymentfrequencyid', function () {
        enrolment.edit();
    });

    $(document).on('keyup', '#paymentfrequencyenrolmentdiscount-discount, #multienrolmentdiscount-discount', function () {
        enrolment.edit();
    });

    var enrolment = {
        edit: function() {
            var paymentFrequency = '<?= $model->paymentFrequencyId; ?>';
            var paymentFrequencyDiscount = '<?= $paymentFrequencyDiscount->discount ?? null; ?>';
            var multiEnrolmentDiscount = '<?= $multipleEnrolmentDiscount->discount ?? null; ?>';
            var paymentFrequencyChanged = $('#enrolment-paymentfrequencyid').val();
            var paymentFrequencyDiscountChanged = $('#paymentfrequencyenrolmentdiscount-discount').val();
            var multiEnrolmentDiscountChanged = $('#multienrolmentdiscount-discount').val();
            if (paymentFrequency != paymentFrequencyChanged || paymentFrequencyDiscount != paymentFrequencyDiscountChanged || multiEnrolmentDiscount != multiEnrolmentDiscountChanged) {
                $('.preview').show();
                if (paymentFrequencyDiscount == paymentFrequencyDiscountChanged && multiEnrolmentDiscount == multiEnrolmentDiscountChanged) {
                    $('.lesson-discount').hide();
                } else {
                    $('.lesson-discount').show();
                }
                if (paymentFrequency == paymentFrequencyChanged) {
                    $('.payment-cycle').hide();
                    $('.payment-request').hide();
                } else {
                    $('.payment-cycle').show();
                    $('.payment-request').show();
                }
            } else {
                $('.preview').hide();
            }
        }
    };
</script>