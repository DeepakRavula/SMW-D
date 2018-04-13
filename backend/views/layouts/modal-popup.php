<?php

use yii\bootstrap\Modal;
?>
<script src="/plugins/bootbox/bootbox.min.js"></script>
<?php
Modal::begin([
    'header' => '<h4 class="m-0">Modal Popup</h4>',
    'id' => 'popup-modal',
    'footer' => $this->render('modal-popup-footer')
]);
?>
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
        $('.modal-save').attr('disabled', true);
        $('.modal-delete').attr('disabled', true);
        $('.modal-cancel').attr('disabled', true);
        $('#modal-form').submit();
        return false;
    });

    $(document).on('afterValidate', '#modal-form', function (event, messages) {
        $.each( messages, function( key, value ) {
            if (value) {
                $('.modal-save').attr('disabled', false);
                $('.modal-delete').attr('disabled', false);
                $('.modal-cancel').attr('disabled', false);
            }
        });
    });

    $(document).off('beforeSubmit', '#modal-form').on('beforeSubmit', '#modal-form', function () {
        $('#modal-spinner').show();
	$('.modal-save').attr('disabled', true);
        $('.modal-delete').attr('disabled', true);
        $('.modal-cancel').attr('disabled', true);
        $.ajax({
            url: $('#modal-form').attr('action'),
            type: 'post',
            dataType: "json",
            data: $('#modal-form').serialize(),
            success: function (response)
            {
                if (response.status)
                {
                    $('#modal-spinner').hide();
                    $('#popup-modal').modal('hide');
                    $(document).trigger("modal-success", response);
                } else {
                    $('#modal-spinner').hide();
                    $('#modal-form').yiiActiveForm('updateMessages', response.errors, true);
                    $(document).trigger("modal-error", response);
                    $('.modal-save').attr('disabled', false);
                    $('.modal-delete').attr('disabled', false);
                    $('.modal-cancel').attr('disabled', false);
                }
            }
        });
        return false;
    });

    $('#popup-modal').on('shown.bs.modal', function () {
	$('#modal-form').find('input[type=text],textarea,select').filter(':visible:first').focus();
        $('#modal-spinner').hide();
    });

    $('#popup-modal').on('hidden.bs.modal', function () {
        $('.modal-save').attr('disabled', false);
        $('.modal-delete').attr('disabled', false);
        $('.modal-cancel').attr('disabled', false);
        $('#modal-delete').hide();
        $('.modal-save').text('Save');
        $('.modal-save').attr('message', null);
        $(document).trigger("modal-close");
    });

    $(document).off('click', '.modal-cancel').on('click', '.modal-cancel', function () {
        $('#modal-spinner').show();
        $('#popup-modal').modal('hide');
        return false;
    });
    
    $(document).off('click', '.modal-delete').on('click', '.modal-delete', function () {
        var message = "Are you sure you want to delete this?";
        bootbox.confirm({
        message: !$.isEmptyObject($(this).attr('message')) ? $(this).attr('message') : message,
            callback: function(result){
                if (result) {
                    $('.bootbox').modal('hide');
                    $('#modal-spinner').show();
                    $.ajax({
                        url : $('.modal-delete').attr('action'),
                        type   : 'post',
                        dataType: "json",
                        data   : $('#modal-form').serialize(),
                        success: function(response)
                        {
                            if (response.status) {
                                $('#modal-spinner').hide();
                                $('#popup-modal').modal('hide');
                                $(document).trigger("modal-delete", response);
                            } else {
                                $('#modal-spinner').hide();
                                $(document).trigger("modal-error", response);
                            }
                        }
                    });
                    return false;
                }
            }
        });
        return false;
    });
</script>
