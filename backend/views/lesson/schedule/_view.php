<?php

use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use yii\helpers\Url;
use common\models\User;

?>
<?php
$toolBoxHtml = $this->render('_button', [
    'model' => $model,
]);
LteBox::begin([
    'type' => LteConst::TYPE_DEFAULT,
    'boxTools' => $toolBoxHtml,
    'title' => 'Schedule',
    'withBorder' => true,
])
?>
<dl class="dl-horizontal">
    <dt>Teacher</dt>
    <dd>
        <a href= "<?= Url::to(['user/view', 'UserSearch[role_name]' => User::ROLE_TEACHER, 'id' => $model->teacherId]) ?>">
            <?= $model->teacher->publicIdentity; ?>
        </a></dd>
    <?php if ($model->isRescheduled() || $model->isUnscheduled()) : ?>
        <?php $date = $model->rootLesson ? $model->rootLesson->date : $model->date ; ?>
        <dt>Original Date</dt>
        <dd><?= (new \DateTime($date))->format('l, F jS, Y'); ?></dd>
    <?php endif; ?>
    <?php if (!($model->isUnscheduled())) : ?>
        <dt>Scheduled Date</dt>
        <dd><?= (new \DateTime($model->date))->format('l, F jS, Y'); ?></dd>
    <?php endif; ?>
    <dt>Time</dt>
    <dd><?= Yii::$app->formatter->asTime($model->date); ?></dd>
    <dt>Duration</dt>
    <dd><?= (new \DateTime($model->duration))->format('H:i'); ?></dd>
<?php if ($model->privateLesson) : ?>
    <dt>Expiry Date</dt>
    <dd><?= Yii::$app->formatter->asDate($model->privateLesson->expiryDate); ?></dd>
<?php endif; ?>

</dl>
<?php LteBox::end() ?>

<script>
    $(document).on('click', '.edit-lesson-schedule', function () {
        $.ajax({
            url: '<?= Url::to(['lesson/update', 'id' => $model->id]); ?>',
            type: 'get',
            dataType: "json",
            success: function (response)
            {
                if (response.status)
                {
                    $('#modal-content').html(response.data);
                    $('#popup-modal').modal('show');
                    $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Edit schedule</h4>');
                    $('#popup-modal .modal-dialog').css({'width': '1000px'});
                }
            }
        });
        return false;
    });
</script>
