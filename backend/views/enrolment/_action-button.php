<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>

<div class="dropdown">
    <i class="fa fa-gear dropdown-toggle" data-toggle="dropdown"></i>
    <ul class="dropdown-menu dropdown-menu-right">
        <li><a id="receive-payments" href="#">Receive Payment</a></li>
        <?php if ($model->course->program->isPrivate()) : ?>
        <li><a class="enrolment-delete" id="enrolment-delete-" href="<?= Url::to(['enrolment/delete', 'id' => $model->id]);?>">Delete</a></li>
        <?php if (Yii::$app->user->can('administrator')) : ?>
            <li><a class="enrolment-full-delete" id="enrolment-full-delete-" href="#">Full Delete</a></li>
        <?php endif; ?>
        <?php endif; ?>
    </ul>
</div>

<script>
	$(document).off('click', '#receive-payments').on('click', '#receive-payments', function () {
        $.ajax({
            url    : '<?= Url::to(['payment/receive', 'PaymentFormLessonSearch[userId]' => $model->customer->id,
                'PaymentFormGroupLessonSearch[userId]' => $model->customer->id ]); ?>',
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
        return false;
    });
</script>