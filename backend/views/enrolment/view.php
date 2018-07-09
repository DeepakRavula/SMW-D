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
<div id="enrolment-delete" style="display: none;" class="alert-danger alert fade in"></div>
<div id="enrolment-delete-success" style="display: none;" class="alert-success alert fade in"></div>
<div id="enrolment-enddate-alert" style="display: none;" class="alert-info alert fade in"></div>

<?=
$this->render('_view-enrolment', [
    'model' => $model,
]);
?>

<div id="enrolment-view-loader" class="spinner" style="display:none">
    <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
    <span class="sr-only">Loading...</span>
</div>
<div class="nav-tabs-custom">
    <?php
    $lessonContent = $this->render('_lesson', [
	'model' => $model,
	'lessonDataProvider' => $lessonDataProvider,
    ]);
    $noteContent = $this->render('_payment-cycle', [
        'model' => $model,
        'paymentCycleDataProvider' => $paymentCycleDataProvider,
    ]);
    $logContent = $this->render('log/index', [
	'logDataProvider' => $logDataProvider,
    ]);
    ?>
    <?php
    LteBox::begin([
	'type' => LteConst::TYPE_DEFAULT,
	'boxTools' =>  false,
	'title' => 'Lessons',
	'withBorder' => true,
    ])
    ?>
    <?=
    $this->render('_private-lesson', [
	'model' => $model,
	'lessonDataProvider' => $lessonDataProvider,
	'lessonCount' => $lessonCount
    ]);
    ?>
    <div class="more-lesson pull-right" id = "admin-login" style = "display:none">
	<a class = "see-more" href = "">See More</a>
    </div>
    <?php LteBox::end() ?>
    <?php
    LteBox::begin([
	'type' => LteConst::TYPE_DEFAULT,
	'boxTools' =>  false,
	'title' => 'History',
	'withBorder' => true,
    ])
    ?>
    <?=
    $this->render('log/index', [
	'logDataProvider' => $logDataProvider,
    ]);
    ?>
    <?php LteBox::end() ?>
    <?php
    $items       = [
    [
        'label' => 'Payment Cycle',
        'content' => $noteContent,
        'options' => [
            'id' => 'payment-cycle',
        ],
    ],
    [
        'label' => 'Lesson',
        'content' => $lessonContent,
        'options' => [
            'id' => 'lesson',
        ],
    ],
    [
        'label' => 'History',
        'content' => $logContent,
        'options' => [
            'id' => 'history',
        ],
    ]
];
    if ($model->course->program->isGroup()) {
	    array_shift($items);
    }
    if($model->course->program->isGroup()) {
    echo Tabs::widget([
        'items' => $items,
    ]);
    }
    ?>
</div>

<script>
        $(document).on('click', '.enrolment-delete', function () {
            var enrolmentId = '<?= $model->id; ?>';
            bootbox.confirm({
                message: "Are you sure you want to delete this enrolment?",
                callback: function (result) {
                    if (result) {
                        $('#enrolment-view-loader').show();
                        $('.bootbox').modal('hide');
                        $.ajax({
                            url: '<?= Url::to(['enrolment/delete']); ?>?id=' + enrolmentId,
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
            var enrolmentId = '<?= $model->id; ?>';
            $.ajax({
                url: '<?= Url::to(['enrolment/full-delete']); ?>?id=' + enrolmentId,
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
                $.pjax.reload({url: url, container: "#payment-cycle-listing", replace: false, async: false, timeout: 4000});
                $.pjax.reload({url: url, container: "#enrolment-view", replace: false, async: false, timeout: 4000});
                $.pjax.reload({url: url, container: "#enrolment-pfi", replace: false, async: false, timeout: 4000});
                $.pjax.reload({url: url, container: "#lesson-index", replace: false, async: false, timeout: 4000});
                $.pjax.reload({url: url, container: "#enrolment-log", replace: false, async: false, timeout: 4000});
                $.pjax.reload({url: url, container: "#lesson-schedule", replace: false, async: false, timeout: 4000});
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
            if (lesson_count > 10) {
                $(".more-lesson").show();
                var type = <?= Lesson::TYPE_PRIVATE_LESSON ?>;
                var student = '<?= $model->student->id ?>';
                var params = $.param({'LessonSearch[student]': student, 'LessonSearch[type]': type, 'LessonSearch[isSeeMore]': true });
                var url = '<?= Url::to(['lesson/index']); ?>?' + params;
                $('.see-more').attr("href", url);
            }
        });
</script>

