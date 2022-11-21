<?php

use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use common\models\Course;
use yii\widgets\Pjax;
use yii\helpers\Url;

?>
<?php Pjax::begin([
    'id' => 'lesson-due-date-details',
    'timeout' => 6000,
]) ?>
<?php
LteBox::begin([
    'type' => LteConst::TYPE_DEFAULT,
    'boxTools' => '<i title="Edit" class="fa fa-pencil" id="edit-due-date"></i>',
    'title' => 'Due Date',
    'withBorder' => true,
])
?>
<dl class="dl-horizontal lesson-due-date">
	<dt>Due Date </dt>
	<dd><?= Yii::$app->formatter->asDate($model->dueDate); ?></dd>
</dl>
<?php LteBox::end()?>
<?php Pjax::end(); ?>
<script>

	$(document).on('click', '#edit-due-date', function () {
        var classroomId = '<?= $model->id; ?>';
		var customUrl = '<?= Url::to(['lesson/edit-due-date']); ?>?id=' + classroomId;
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
        return false;
    });
</script>