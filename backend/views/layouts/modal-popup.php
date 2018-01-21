<?php

use yii\bootstrap\Modal;

?>

<?php Modal::begin([
    'header' => '<h4 class="m-0">Modal Popup</h4>',
    'id' => 'popup-modal',
    'footer' => $this->render('/layouts/_submit-button')
]); ?>

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
        $.ajax({
            url    : $('#modal-form').attr('action'),
            type   : 'post',
            dataType: "json",
            data   : $('#modal-form').serialize(),
            success: function(response)
            {
                if(response.status)
                {
                    $('#popup-modal').modal('hide');
                    $.pjax.reload({container: '#lesson-index', timeout: 6000, async:false});
                    $('#enrolment-delete-success').html("Lessons are changed to different course").
                                fadeIn().delay(5000).fadeOut();
                } else {
                    $('#modal-form').yiiActiveForm('updateMessages', response.errors, true);
                }
            }
        });
        return false;
    });
    
    $(document).off('click', '.modal-cancel').on('click', '.modal-cancel', function () {
        $('#popup-modal').modal('hide');
        $.pjax.reload({container: '#lesson-index', timeout: 6000, async:false});
        return false;
    });
</script>