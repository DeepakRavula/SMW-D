<?php

use yii\bootstrap\Modal;

?>

<?php Modal::begin([
    'header' => '<h4 class="m-0">Modal Popup</h4>',
    'id' => 'popup-modal',
    'footer' => $this->render('/layouts/modal-popup-footer')
]); ?>
<div id="modal-spinner" class="spinner" style="display:none">
    <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
    <span class="sr-only">Loading...</span>
</div>
<div id="modal-popup-error-notification" style="display: none;" class="alert-danger alert fade in"></div>
<div id="modal-popup-success-notification" style="display: none;" class="alert-success alert fade in"></div>

<div id="modal-content"></div>

<?php Modal::end(); ?>


<script>
    $(document).off('click', '.modal-save').on('click', '.modal-save', function () {
        $('#modal-form').submit();
        return false;
    });
    
    $(document).off('beforeSubmit', '#modal-form').on('beforeSubmit', '#modal-form', function () {
        $('#modal-spinner').show();
        $.ajax({
            url    : $('#modal-form').attr('action'),
            type   : 'post',
            dataType: "json",
            data   : $('#modal-form').serialize(),
            success: function(response)
            {
                if(response.status)
                {
                    $('#modal-spinner').hide();
                    $('#popup-modal').modal('hide');
                    $(document).trigger( "modal-success", response);
                } else {
                    $('#modal-spinner').hide();
                    $('#modal-form').yiiActiveForm('updateMessages', response.errors, true);
                    $(document).trigger( "modal-error", response);
                }
            }
        });
        return false;
    });
    
    $('#popup-modal').on('shown.bs.modal', function () {
        $('#modal-spinner').hide();
    });
    
    $('#popup-modal').on('hidden.bs.modal', function () {
        $(document).trigger( "modal-close");
    });
    
    $(document).off('click', '.modal-cancel').on('click', '.modal-cancel', function () {
        $('#modal-spinner').show();
        $('#popup-modal').modal('hide');
        return false;
    });
</script>