<?php

use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $model common\models\Student */
/* @var $form yii\bootstrap\ActiveForm */
?>
<button class ="btn btn-primary pull-right m-t-25" id="bulk-reschedule-teacher" data-teacherId = <?= $teacherId?> >Bulk Reschedule</button>
<div id="teacher-schedule-calendar"></div>
<script>
  $(document).on('click', '#bulk-reschedule-teacher', function () {
    var teacherId = $(this).attr('data-teacherId');
    var params = $.param({ 'PrivateLesson[selectedTeacherId]': teacherId});
    var customUrl = '<?= Url::to(['private-lesson/teacher-bulk-reschedule']); ?>?' +params;
            $.ajax({
                url    : customUrl,
                type   : 'get',
                dataType: "json",
                data   : $(this).serialize(),
                success: function(response)
                {
                    if(response.status)
                    {
                        $('#popup-modal').modal('show');
                        $('#modal-content').html(response.data);
                    }
                }
            });
    });
</script>