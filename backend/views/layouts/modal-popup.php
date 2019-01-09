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
<div id="modal-popup-warning-notification" style="display: none;" class="alert-warning alert fade in"></div>

<div id="modal-content"></div>

<?php Modal::end(); ?>


<script>
    var modal = {
        disableButtons: function() {
            $('.modal-save').attr('disabled', true);
            $('.modal-button').attr('disabled', true);
            $('.modal-save-all').attr('disabled', true);
            $('.modal-back').attr('disabled', true);
            $('.modal-delete').attr('disabled', true);
            $('.modal-cancel').attr('disabled', true);
            $('.modal-mail').attr('disabled', true);
        },
        enableButtons: function() {
            $('.modal-save').attr('disabled', false);
            $('.modal-button').attr('disabled', false);
            $('.modal-delete').attr('disabled', false);
            $('.modal-cancel').attr('disabled', false);
            $('.modal-save-all').attr('disabled', false);
            $('.modal-back').attr('disabled', false);
            $('.modal-mail').attr('disabled', false);
        },
        restoreButtonSettings: function() {
            $('.modal-delete').hide();
            $('.modal-button').hide();
            $('#modal-save').text('Save');
            $('#modal-save').removeClass();
            $('#modal-save').addClass('btn btn-info modal-save');
            $('.modal-save-all').hide();
            $('.modal-back').hide();
            $('.modal-mail').hide();
            $('#modal-save').attr('message', null);
            $('#modal-popup-warning-notification').fadeOut();
        },
        renderUrlData: function(url) {
            $.ajax({
                url : url,
                type   : 'get',
                dataType: "json",
                success: function(response)
                {
                    $('#modal-spinner').hide();
                    if (response.status) {
                        $('#modal-content').html(response.data);
                    } else {
                        $(document).trigger("modal-error", response);
                    }
                }
            });
        }
    };

    $(document).off('click', '.modal-save').on('click', '.modal-save', function () {
        modal.disableButtons();
        $('#modal-form').submit();
        return false;
    });

    $(document).on('afterValidate', '#modal-form', function (event, messages, errorAttributes) {
        if (errorAttributes.length > 0) {
            modal.enableButtons();
        }
    });

    $(document).off('beforeSubmit', '#modal-form').on('beforeSubmit', '#modal-form', function () {
        $('#modal-spinner').show();
	    modal.disableButtons();
        $.ajax({
            url: $('#modal-form').attr('action'),
            type: 'post',
            dataType: "json",
            data: $('#modal-form').serialize(),
            success: function (response)
            {
                $('#modal-spinner').hide();
                if (response.status)
                {
                    $('#modal-spinner').hide();
                    if (!$.isEmptyObject(response.data)) {
                        $('#modal-content').html(response.data);
                        $('.modal-back').show();
                        $(document).trigger("modal-next", response);
                    } else if (!$.isEmptyObject(response.dataUrl)) {
                        modal.renderUrlData(response.dataUrl);
                    } else {
                        $(document).trigger("modal-success", response);
                        $('#popup-modal').modal('hide'); 
                    }
                } else {
                    $('#modal-form').yiiActiveForm('updateMessages', response.errors, true);
                    $(document).trigger("modal-error", response);
                }
                modal.enableButtons();
            }
        });
        return false;
    });

    $('#popup-modal').on('shown.bs.modal', function () {
        var isDatePicker = $('#modal-form').find('input[type=text],textarea,select').filter(':visible:first').attr('class');
        if (isDatePicker != 'form-control hasDatepicker' && isDatePicker != 'form-control no-focus'){
	        $('#modal-form').find('input[type=text],textarea,select').filter(':visible:first').focus();
        }
        $('#modal-spinner').hide();
    });

    $('#popup-modal').on('hidden.bs.modal', function () {
        modal.enableButtons();
        modal.restoreButtonSettings();
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
                            $('#modal-spinner').hide();
                            if (response.status) {
                                $('#popup-modal').modal('hide');
                                $(document).trigger("modal-delete", response);
                            } else {
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
