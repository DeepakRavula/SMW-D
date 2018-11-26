<?php

use yii\helpers\Url;
use common\Models\User;
use yii\widgets\Pjax;
?>

<?php Pjax::begin([
    'id' => 'lesson-more-action',
    'timeout' => 6000,
]) ?>
<div class="pull-right">
    <div class="dropdown">
        <i class="fa fa-gear dropdown-toggle" data-toggle="dropdown"></i>
        <ul class="dropdown-menu dropdown-menu-right">
            <li><a id="receive-payment" href="#">Receive Payment</a></li>
            <li><a id="lesson-mail-button" href="#">Mail</a></li>
            <?php if ($model->isPrivate()) : ?>
                <?php if ($model->canExplode()) : ?>
                    <li><a id="split-lesson" href="#">Explode</a></li>
                <?php endif; ?>
                <?php if ($model->isDeletable()) : ?>
                    <li><a id="lesson-delete" href="#">Delete</a></li>
                <?php endif; ?>
            <?php endif; ?>
        </ul>
    </div>
</div>
<?php Pjax::end(); ?>	

<script>
    $(document).on('click', '#split-lesson', function () {
        $( "#split-lesson" ).addClass("multiselect-disable");
        $.ajax({
            url    : '<?= Url::to(['private-lesson/split', 'id' => $model->id]); ?>',
            type   : 'get',
            dataType: 'json'
        });
        return false;
    });

    $(document).off('click', '#receive-payment').on('click', '#receive-payment', function () {
        $.ajax({
            url    : '<?= Url::to(['payment/receive', 'PaymentFormLessonSearch[userId]' => $model->customer ? $model->customer->id : null, 
                'PaymentFormGroupLessonSearch[userId]' => $model->customer ? $model->customer->id : null]); ?>',
            type   : 'get',
            dataType: 'json',
            success: function(response)
            {
                if (response.status) {
                    $('#modal-content').html(response.data);
                    $('#popup-modal').modal('show');
                    
                }
            }
        });
    });

</script>
