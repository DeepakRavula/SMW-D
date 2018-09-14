<?php

use yii\bootstrap\Tabs;
use common\models\ExamResult;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use common\models\Note;
use kartik\select2\Select2Asset;
use yii\widgets\Pjax;
Select2Asset::register($this);
use kartik\time\TimePickerAsset;
TimePickerAsset::register($this);

/* @var $this yii\web\View */
/* @var $model common\models\Student */
$this->title = $model->fullName;
$this->params['label'] = $this->render('_title', [
    'model' => $model,
]);?>
<div id="student-delete" style="display: none;" class="alert-danger alert fade in"></div>
<div id="enrolment-delete" style="display: none;" class="alert-danger alert fade in"></div>
<div id="enrolment-delete-success" style="display: none;" class="alert-success alert fade in"></div>
<script src="/plugins/bootbox/bootbox.min.js"></script>
<br>
<div class="row">
    <?= $this->render('_profile', [
        'model' => $model,
    ]); ?>
</div>
<div class="row">
<?php Pjax::begin(['id' => 'enrolment-list']);?>
    <?= $this->render('enrolment/view', [
        'model' => $model,
        'enrolmentDataProvider' => $enrolmentDataProvider,
        'enrolmentSearchModel'=>$enrolmentSearchModel,
    ]); ?>
<?php Pjax::end();?>
</div>
<div class="row">
    <?= $this->render('exam-result/view', [
        'model' => new ExamResult(),
        'studentModel' => $model,
        'examResultDataProvider' => $examResultDataProvider
    ]); ?>
</div>

<div class="nav-tabs-custom">
    <?php $lessonContent = $this->render('_lesson', [
            'lessonDataProvider' => $lessonDataProvider,
            'model' => $model,
            'allEnrolments' => $allEnrolments,
            'lessonCount' => $lessonCount,
        ]);

        $groupLessonContent = $this->render('_group-lesson', [
            'groupLessonDataProvider' => $groupLessonDataProvider,
            'model' => $model,
            'allEnrolments' => $allEnrolments,
        ]);

        $completedLessonContent = $this->render('_completed-lesson', [
            'completedLessonDataProvider' => $completedLessonDataProvider,
            'model' => $model,
            'allEnrolments' => $allEnrolments,
        ]);

        $unscheduledLessonContent = $this->render('_unscheduledLesson', [
            'model' => $model,
            'unscheduledLessonSearchModel' => $unscheduledLessonSearchModel,
            'dataProvider' => $unscheduledLessonDataProvider,
        ]);

        $logContent = $this->render('log/index', [
            'model' => $model,
            'logs' => $logs
        ]);

        $noteContent = $this->render('note/view', [
            'model' => new Note(),
            'studentModel' => $model,
            'noteDataProvider' => $noteDataProvider
        ]);

        if (!empty($model->studentCsv)) {
            $csvContent = $this->render('csv', [
                'model' => $model->studentCsv,
                'studentModel' => $model
            ]);
        }
        $items = [
                [
                'label' => 'Private Lessons',
                'content' => $lessonContent,
                'options' => [
                    'id' => 'lesson',
                ],
            ],
            [
                'label' => 'Group Lessons',
                'content' => $groupLessonContent,
                'options' => [
                    'id' => 'group-lesson',
                ],
            ],
            [
                'label' => 'Absent Lessons',
                'content' => $completedLessonContent,
                'options' => [
                    'id' => 'completed-lesson',
                ],
            ],
                [
                'label' => 'Unscheduled Lessons',
                'content' => $unscheduledLessonContent,
                'options' => [
                    'id' => 'unscheduledLesson',
                ],
            ],
                [
                'label' => 'Comments',
                'content' => $noteContent,
                'options' => [
                    'id' => 'note',
                ],
            ],
 
            [
                'label' => 'History',
                'content' => $logContent,
                'options' => [
                    'id' => 'log',
                ],
            ],
        ];
        if (!empty($model->studentCsv)) {
            array_push($items, [
                'label' => 'CSV',
                'content' => $csvContent,
                'options' => [
                    'id' => 'csv',
                ],
            ]);
        }
        ?>
    <?= Tabs::widget([
        'items' => $items
    ]); ?>
</div>

<?php Modal::begin([
    'header' => '<h4 class="m-0">Student Merge</h4>',
    'id' => 'student-merge-modal',
]); ?>
<div id="student-merge-content"></div>
<?php Modal::end(); 
    $customerDiscount = $model->customer->customerDiscount->value ?? null;
?>

<script>
    $(document).on('modal-success', function(event, params) {
        if (!$.isEmptyObject(params.url)) {
            window.location.href = params.url;
        }
        else
        {
                    $.pjax.reload({container: "#enrolment-list",timeout: 6000, async:false});
                    $.pjax.reload({container: "#lesson-index",timeout: 6000, async:false});
                    $.pjax.reload({container: "#student-log",timeout: 6000, async:false});
                    $.pjax.reload({container: "#student-exam-result-listing",timeout: 6000, async:false});
                    $.pjax.reload({container: "#student-note",timeout: 6000, async:false});
                    $.pjax.reload({container: "#lesson-index",timeout: 6000, async:false});
        }
        return false;
    });

    $(document).on('click', '#add-private-enrol', function () {
        var customerDiscount = '<?= $customerDiscount;?>';
        $.ajax({
            url    : '<?= Url::to(['course/create-enrolment-basic', 'studentId' => $model->id, 'isReverse' => false]); ?>',
            type   : 'get',
            dataType: "json",
            success: function(response)
            {
                if(response.status)
                {
                    $('#modal-content').html(response.data);
                    $('#popup-modal').modal('show');
                    $('.modal-save').show();
                    $('.modal-save').text('Next');
                    $('#popup-modal').find('.modal-header').html('<h4 class="m-0">New Enrolment Basic</h4>');
                    $('#popup-modal .modal-dialog').css({'width': '600px'});
                    $('#customer-discount').val(customerDiscount);
                }
            }
        });
        return false;
    });

    $(document).on('click', '#add-group-enrol', function () {
        $.ajax({
            url    : $(this).attr('href'),
            type: 'get',
            dataType: "json",
            success: function (response)
            {
                if (response.status)
                {
                    $('#group-enrol-modal .modal-body').html(response.data);
                    $('#group-enrol-modal').modal('show');
                    $('#group-enrol-modal .modal-dialog').css({'width': '800px'});
                } 
            }
        });
        return false;
    });

    $(document).on('change keyup paste', '#course-name', function (e) {
        var courseName = $(this).val();
        var id = '<?= $model->id; ?>';
        var params = $.param({'studentId' : id, 'courseName' : courseName});
        $.ajax({
            url    : '<?= Url::to(['course/fetch-group']); ?>?' + params,
            type   : 'get',
            dataType: 'json',
            success: function(response)
            {
                if(response.status) {
                    $('#group-enrol-modal .modal-body').html(response.data);
                }
            }
        });
        return false;
    });

    $(document).on('click', '#student-merge', function () {
        $.ajax({
            url    : '<?= Url::to(['student/merge', 'id' => $model->id]); ?>',
            type   : 'get',
            dataType: "json",
            data   : $(this).serialize(),
            success: function(response)
            {
                if(response.status)
                {
                    $('#modal-content').html(response.data);
                    $('#popup-modal').modal('show');
                }
            }
        });
        return false;
    });

    $(document).on('click', '.group-enrol-btn', function() {
        $('#course-spinner').show();
        var courseId = $(this).attr('data-key');
        var params = $.param({'courseId': courseId });
        $.ajax({
            url    : '<?= Url::to(['enrolment/group', 'studentId' => $model->id]); ?>&' + params,
            type: 'post',
            success: function(response) {
                if (response.status) {
                    $('#course-spinner').hide();
                    $.pjax.reload({container: "#enrolment-list", replace: false, async: false, timeout: 6000});
                    $.pjax.reload({container: "#student-log", replace: false, async: false, timeout: 6000});
                    $('#group-enrol-modal').modal('hide');
                    $('#course-spinner').hide();
                    $('#group-enrol-modal').modal('hide');
                    window.location.href = response.url;
                }
            }
        });
        return false;
    });
 

   
    $(document).on('click', '.note-cancel-button', function (e) {
        $('#student-note-modal').modal('hide');
        return false;
    });

    $(document).on('click', '.student-note', function (e) {
        $('#note-content').val('');
        $('#student-note-modal').modal('show');
        return false;
    });

    $(document).on('click', '.extra-lesson-cancel-button', function () {
        $('#new-lesson-modal').modal('hide');
        return false;
    });

     $(document).on('click', '.add-new-exam-result,#student-exam-result-listing  tbody > tr', function () {
        var examResultId = $(this).data('key');
        var studentId  = <?= $model->id ?>;
            if (!examResultId) {
                var customUrl = '<?= Url::to(['exam-result/create', 'studentId' => $model->id]); ?>';
            } else {
                var customUrl = '<?= Url::to(['exam-result/update']); ?>?id=' + examResultId;
                var url = '<?= Url::to(['exam-result/delete']); ?>?id=' + examResultId;
                $('.modal-delete').show();
                $(".modal-delete").attr("action",url);
            }
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
                        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Exam Result</h4>');
                        $('#modal-content').html(response.data);
                    }
                }
            });
            return false;
        });

    $(document).on('click', '.enrolment-delete', function () {
        var enrolmentId = $(this).parent().parent().data('key');
        bootbox.confirm({
            message: "Are you sure you want to delete this enrolment?",
            callback: function (result) {
                if (result) {
                    $('.bootbox').modal('hide');
                    $.ajax({
                        url: '<?= Url::to(['enrolment/delete']); ?>?id=' + enrolmentId,
                        type: 'post',
                        success: function (response)
                        {
                            if (response.status)
                            {
                                $.pjax.reload({container: '#enrolment-grid', skipOuterContainers: true, timeout: 6000});
                            } else {
                                $('#enrolment-delete').html('You are not allowed to delete this enrolment.').
                                        fadeIn().delay(3000).fadeOut();
                            }
                        }
                    });
                    return false;
                }
            }
        });
        return false;
    });

    $(document).on('beforeSubmit', '#lesson-form', function (e) {
        $.ajax({
            url: $(this).attr('action'),
            type: 'post',
            dataType: "json",
            data: $(this).serialize(),
            success: function (response)
            {
                if (response.status)
                {
                    $('#new-lesson-modal').modal('hide');
                    window.location.href = response.url;
                }
            }
        });
        return false;
    });
    
    $(document).on('click', '#button', function () {
        $.ajax({
            url: $(this).attr('href'),
            type: 'POST',
            dataType: 'json',
            success: function (response)
            {
                if (response) {
                    var url = response.url;
                    $.pjax.reload({url: url, container: '#student-exam-result-listing', timeout: 6000});
                }
            }
        });
        return false;
    });

    $(document).on('beforeSubmit', '#student-note-form', function (e) {
        $.ajax({
            url: '<?= Url::to(['note/create', 'instanceId' => $model->id,
                'instanceType' => Note::INSTANCE_TYPE_STUDENT]); ?>',
            type: 'post',
            dataType: "json",
            data: $(this).serialize(),
            success: function (response)
            {
                if (response.status)
                {
                    $('.student-note-content').html(response.data);
                }
            }
        });
        return false;
    });
    
    $(document).on('click', '.student-profile-edit-button', function () {
        $.ajax({
            url : '<?= Url::to(['student/update', 'id' => $model->id, 'userModel' =>$model->customer]); ?>',
            type: 'post',
            dataType: "json",
            data: $(this).serialize(),
            success: function (response)
            {
                if (response.status)
                {
                    $('#popup-modal').modal('show');
                    $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Edit Profile</h4>');
                    $('#modal-content').html(response.data);
                }
            }
        });
        return false;
    });

     $(document).on('click', '.student-delete', function () {
        bootbox.confirm({
            message: "Are you sure you want to delete this student?",
            callback: function (result) {
                if (result) {
                    $('.bootbox').modal('hide');
                    $.ajax({
                        url: '<?= Url::to(['student/delete', 'id' => $model->id]); ?>',
                        type   : 'post',
                        dataType: "json",
                        data: $(this).serialize(),
                        success: function (response)
                        {
                            if (response.status) {
                                window.location.href = response.url;
                            } else {
                                $('#student-delete').html('You are not allowed to delete this student.').fadeIn().delay(3000).fadeOut();
                            }
                        }
                    });
                }
            }
        });
        return false;
    });
</script>
