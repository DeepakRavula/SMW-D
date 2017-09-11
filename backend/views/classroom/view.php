<?php

use yii\bootstrap\Tabs;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\ClassRoom */

?>
<div class="row">
    <?php
    echo $this->render('unavailability/view', [
        'model' => $model,
        'unavailabilityDataProvider' => $unavailabilityDataProvider
    ]);

    ?>
</div>
<script>
    $(document).ready(function () {
        $(document).on("click", ".classroom-unavailability,#classroom-unavailability-grid tbody > tr", function () {
            var unavailabilityId = $(this).data('key');
            var classroomId = <?= $model->id ?>;
            if (unavailabilityId === undefined)
            {
                var customUrl = '<?= Url::to(['classroom-unavailability/create']); ?>?classroomId=' + classroomId;
                } else
                {
                    var customUrl = '<?= Url::to(['classroom-unavailability/update']); ?>?id=' + unavailabilityId;
                }
                $.ajax({
                    url: customUrl,
                    type: 'get',
                    dataType: "json",
                    success: function (response)
                    {
                        if (response.status)
                        {
                            $('#classroom-unavailability-modal .modal-body').html(response.data);
                            $('#classroom-unavailability-modal').modal('show');
                        } else {
                            $('#classroom-unavailability-form').yiiActiveForm('updateMessages',
                                    response.errors
                                    , true);
                        }
                    }
                });

                return false;
            });
        $(document).on('beforeSubmit', '#classroom-unavailability-form', function (e) {
            $.ajax({
                url: $(this).attr('action'),
                type: 'post',
                dataType: "json",
                data: $(this).serialize(),
                success: function (response)
                {
                    if (response.status)
                    {
                        $.pjax.reload({container: '#classroom-unavailability-grid', timeout: 4000});
                                $('#classroom-unavailability-modal').modal('hide');
                            } else
                            {
                                $('#classroom-unavailability-form').yiiActiveForm('updateMessages',
                                        response.errors
                                        , true);
                            }
                        }
                    });
                    return false;
                });
            });
</script>