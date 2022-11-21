<?php

use yii\helpers\Url;
use common\Models\User;
use yii\widgets\Pjax;
?>
<?php Pjax::begin([
    'id' => 'group-lesson-more-action',
    'timeout' => 6000,
]) ?> 
<div class="pull-right">
    <div class="dropdown">
        <i class="fa fa-gear dropdown-toggle" data-toggle="dropdown"></i>
        <ul class="dropdown-menu dropdown-menu-right">
            <li><a id="group-course-print" href="#">Print</a></li>
            <li><a id="group-course-delete" href="#">Delete</a></li>
        </ul>
    </div>
</div>
<?php Pjax::end(); ?>

<script>
    $(document).off('click', '#group-course-print').on('click', '#group-course-print', function () {
        var url = '<?= Url::to(['print/course' ,'id' => $model->id]); ?>';
        window.open(url,'_blank');
        return false;
    });

     $(document).on('click', '#group-course-delete', function () {
            bootbox.confirm({
                message: "Are you sure you want to delete this group course?",
                callback: function (result) {
                    if (result) {
                        $('.bootbox').modal('hide');
                        $.ajax({
                            url: '<?= Url::to(['course/group-course-delete', 'id' => $model->id]); ?>',
                            dataType: "json",
                            data: $(this).serialize(),
                            success: function (response)
                            {
                                if (response.status) {
                                    window.location.href = response.url;
                                } else {
                                    $('#error-notification').html(response.error).fadeIn().delay(5000).fadeOut();
                                }
                            }
                        });
                    }
                }
            });
            return false;
        });
</script>