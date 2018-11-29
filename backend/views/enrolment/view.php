<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Tabs;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use common\models\Lesson;

$this->title = $model->course->program->name;
$this->params['label'] = $this->render('_title', [
    'model' => $model,
]);
$this->params['action-button'] = $this->render('_action-button', [
    'model' => $model,
]);
?>

<script src="/plugins/bootbox/bootbox.min.js"></script>
<div id="group-enrolment-error-notification" style="display:none;" class="alert-danger alert fade in"></div>
<div id="enrolment-delete" style="display: none;" class="alert-danger alert fade in"></div>
<div id="enrolment-delete-success" style="display: none;" class="alert-success alert fade in"></div>
<div id="enrolment-enddate-alert" style="display: none;" class="alert-info alert fade in"></div>

<?= $this->render('_view-enrolment', [
    'model' => $model,
    'scheduleHistoryDataProvider' => $scheduleHistoryDataProvider
]); ?>

<div id="enrolment-view-loader" class="spinner" style="display:none">
    <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
    <span class="sr-only">Loading...</span>
</div>

<?php $logContent = $this->render('log/index', [
	'logDataProvider' => $logDataProvider,
]); ?>

<div class='row'>
    <div class= 'col-md-6'>
        <?php LteBox::begin([
            'type' => LteConst::TYPE_DEFAULT,
            'boxTools' => false,
            'title' => 'Lessons',
            'withBorder' => true,
        ]) ?>

        <?php if ($model->course->program->isPrivate()) {
            echo $this->render('_private-lesson', [
                'model' => $model,
                'lessonDataProvider' => $lessonDataProvider,
                'lessonCount' => $lessonCount
            ]);
        } else {
            echo $this->render('_private-lesson', [
                'model' => $model,
                'groupLessonDataProvider' => $groupLessonDataProvider,
                'lessonCount' => $lessonCount
            ]);    
        } ?>

        <div class="more-lesson pull-right" id = "admin-login" style = "display:none">
            <a class = "see-more" href = "">Show More</a>
        </div>

        <?php LteBox::end() ?>
    </div>
    
    <?php if ($model->course->program->isPrivate()) : ?>
        <div class='col-md-6'>
            <?php LteBox::begin([
                'type' => LteConst::TYPE_DEFAULT,
                'boxTools' => false,
                'title' => 'Payment Cycles',
                'withBorder' => true,
            ]) ?>

            <?= $this->render('_payment-cycle', [
                'model' => $model,
                'paymentCycleDataProvider' => $paymentCycleDataProvider,
            ]); ?>
            <?php LteBox::end() ?>
        </div>
    <?php endif; ?>
</div>

<?php LteBox::begin([
	'type' => LteConst::TYPE_DEFAULT,
	'boxTools' => false,
	'title' => 'History',
	'withBorder' => true,
]) ?>

<?= $this->render('log/index', [
	'logDataProvider' => $logDataProvider,
]); ?>

<?php LteBox::end() ?>

<script>
    $(document).on('click', '.enrolment-delete', function () {
        bootbox.confirm({
            message: "Are you sure you want to delete this enrolment?",
            callback: function (result) {
                if (result) {
                    $('#enrolment-view-loader').show();
                    $('.bootbox').modal('hide');
                    $.ajax({
                        url: '<?= Url::to(['enrolment/delete', 'id' => $model->id]); ?>',
                        dataType: "json",
                        data: $(this).serialize(),
                        success: function (response)
                        {
                            $('#enrolment-view-loader').hide();
                            if (response.status) {
                                window.location.href = response.url;
                            } else {
                                $('#enrolment-delete').html('You are not allowed to delete this enrolment.').fadeIn().delay(3000).fadeOut();
                            }
                        }
                    });
                }
            }
        });
        return false;
    });

    $(document).on('click', '.enrolment-full-delete', function () {
        $.ajax({
            url: '<?= Url::to(['enrolment/full-delete', 'id' => $model->id]); ?>',
            type: 'get',
            dataType: "json",
            success: function (response)
            {
                if (response.status)
                {
                    $('#popup-modal').modal('show');
                    $('#modal-content').html(response.data);
                }
            }
        });
        return false;
    });

    $(document).on('click', '.enrolment-edit', function () {
        $.ajax({
            url: '<?= Url::to(['enrolment/update', 'id' => $model->id]); ?>',
            type: 'get',
            dataType: "json",
            success: function (response)
            {
                if (response.status)
                {
                    $('#popup-modal').modal('show');
                    $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Enrolment Edit</h4>');
                    $('.modal-save').show();
                    $('.modal-save').text('Next');
                    $('#modal-content').html(response.data);
                    $('#popup-modal .modal-dialog').css({'width': '500px'});
                }
            }
        });
    });

    var paymentFrequency = {
        onEditableSuccess: function () {
            var url = "<?php echo Url::to(['enrolment/view', 'id' => $model->id]); ?>"
            $.pjax.reload({url: url, container: "#enrolment-view", replace: false, async: false, timeout: 4000});
            $.pjax.reload({url: url, container: "#enrolment-pfi", replace: false, async: false, timeout: 4000});
            $.pjax.reload({url: url, container: "#enrolment-log", replace: false, async: false, timeout: 4000});
            $.pjax.reload({url: url, container: "#lesson-schedule", replace: false, async: false, timeout: 4000});
            $.pjax.reload({url: url, container: "#enrolment-lesson-index", replace: false, async: false, timeout: 4000});
            $.pjax.reload({url: url, container: "#payment-cycle-listing", replace: false, async: false, timeout: 4000});
        }
    };

    $(document).on('modal-success', function (event, params) {
        if (params.url) {
            window.location.href = params.url;
        } else {
            paymentFrequency.onEditableSuccess();
        }
        return false;
    });
    
    $(document).ready(function () {
        var lesson_count = '<?= $lessonCount; ?>';
        if (lesson_count > 12) {
            var private = <?= $model->course->program->isPrivate(); ?>;
            if (private) {
                $(".more-lesson").show();
                var url = '<?= Url::to(['lesson/index', 'LessonSearch[studentId]' => $model->student->id, 'LessonSearch[programId]' => $model->program->id, 'LessonSearch[type]' => Lesson::TYPE_PRIVATE_LESSON, 'LessonSearch[student]' => $model->student->fullName, 'LessonSearch[isSeeMore]'=> true]); ?>';
                $('.see-more').attr("href", url);
            }
        } else {
            $(".more-lesson").hide();
        }
    });
</script>