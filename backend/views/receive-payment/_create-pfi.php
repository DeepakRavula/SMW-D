<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\PaymentMethods */
/* @var $form yii\bootstrap\ActiveForm */
?>

    <?php $form = ActiveForm::begin([
        'id' => 'modal-form',
        'action' => Url::to(['proforma-invoice/create']),
    ]); ?>
    <?php ActiveForm::end(); ?>
    <?= Html::label('Lessons', ['class' => 'admin-login']) ?>
    <?= $this->render('/receive-payment/_lesson-line-item', [
        'model' => $model,
        'isCreatePfi' => true,
        'lessonLineItemsDataProvider' => $lessonLineItemsDataProvider,
        'searchModel' => $searchModel,
    ]);
    ?>

    <?= Html::label('Invoices', ['class' => 'admin-login']) ?>
    <?= $this->render('/receive-payment/_invoice-line-item', [
        'model' => $model,
        'isCreatePfi' => true,
        'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
        'searchModel' => $searchModel,
    ]);
    ?>

<script>
    var createPFI = {
        setAction: function() {
            var userId = '<?= $searchModel->userId ?>';
            var lessonIds = $('#lesson-line-item-grid').yiiGridView('getSelectedRows');
            var invoiceIds = $('#invoice-line-item-grid').yiiGridView('getSelectedRows');
            var params = $.param({ 'PaymentFormLessonSearch[userId]': userId, 'PaymentFormLessonSearch[lessonIds]': lessonIds, 
                'ProformaInvoice[invoiceIds]': invoiceIds });
            var url = '<?= Url::to(['proforma-invoice/create']) ?>?' + params;
            $('#modal-form').attr('action', url);
            return false;
        }
    };

    $(document).ready(function () {
        $('#popup-modal .modal-dialog').css({'width': '1000px'});
        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Create PFI</h4>');
        $('.modal-save').show();
        $('.modal-save').text('Create PFI');
        $('.select-on-check-all').prop('checked', true);
        createPFI.setAction();
    });

    $(document).off('pjax:success', '#lesson-line-item-listing').on('pjax:success', '#lesson-line-item-listing', function () {
        createPFI.setAction();
        return false;
    });

    $(document).off('change', '#lesson-line-item-grid, #invoice-line-item-grid, .select-on-check-all, input[name="selection[]"]').on('change', '#lesson-line-item-grid, #invoice-line-item-grid, .select-on-check-all, input[name="selection[]"]', function () {
        createPFI.setAction();
        return false;
    });

    $(document).on('modal-success', function(event, params) {
        if (params.url) {
            window.location.href = params.url;
        }
        return false;
    });
</script>
