<?php

use yii\helpers\Url;
use kartik\datetime\DateTimePickerAsset;
DateTimePickerAsset::register($this);
$this->title = 'Review Lessons';
?>

<div class="row">
    <div class="col-md-6">
        <?= $this->render('review/_details', [
            'model' => $model,
            'courseModel' => $courseModel,
        ]); ?>
    </div>
    <div class="col-md-6">
        <?php if (empty($rescheduleBeginDate)) : ?>
            <?= $this->render('review/_summary', [
                'holidayConflictedLessonIds' => $holidayConflictedLessonIds,
                'unscheduledLessonCount' => $unscheduledLessonCount,
                'lessonCount' => $lessonCount,
                'conflictedLessonIdsCount' => $conflictedLessonIdsCount,
            ]); ?>
        <?php endif; ?>
    </div>
</div>

<?php
$hasConflict = false;
if ($conflictedLessonIdsCount > 0) {
    $hasConflict = true;
} ?>

<div class="row">
    <div class="col-md-12">
        <?= $this->render('review/_view', [
            'searchModel' => $searchModel,
            'lessonDataProvider' => $lessonDataProvider,
            'conflicts' => $conflicts
        ]); ?>
    </div>
</div>

<?= $this->render('review/_button', [
    'hasConflict' => $hasConflict,
    'rescheduleBeginDate' => $rescheduleBeginDate,
    'rescheduleEndDate' => $rescheduleEndDate,
    'courseId' => $courseId,
    'courseModel' => $courseModel,
    'enrolmentType' => $enrolmentType,
]); ?>

<div id="enrolment-loader" class="spinner" style="display:none">
    <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
    <span class="sr-only">Loading...</span>
</div>

<script>
    var review = {
        onEditableGridSuccess: function () {
            $.ajax({
                url: "<?php echo Url::to(['lesson/fetch-conflict', 'courseId' => $courseId]); ?>",
                type: "GET",
                dataType: "json",
                success: function (response)
                {
                    if (response.hasConflict) {
                        $("#confirm-button").attr("disabled", true);
                        $('#confirm-button').bind('click', false);
                    } else {
                        $("#confirm-button").removeAttr('disabled');
                        $('#confirm-button').unbind('click', false);
                    }
                }
            });
            return true;
        }
    }

    $(document).off('click', '#confirm-button').on('click', '#confirm-button', function () {
        $('#confirm-button').attr('disabled', true);
        $('.review-cancel').attr('disabled', true);
        $('#enrolment-loader').show();
    });
    
    $(document).ready(function () {
        if ($('#confirm-button').attr('disabled')) {
            $('#confirm-button').bind('click', false);
        }
    });
        
    $(document).on('change', '#lessonsearch-showallreviewlessons', function () {
        var showAllReviewLessons = $(this).is(":checked");
        var startDate = '<?= $rescheduleBeginDate; ?>';
        var endDate = '<?= $rescheduleEndDate; ?>';
        if (startDate && endDate) {
            var params = $.param({
                'LessonSearch[showAllReviewLessons]': (showAllReviewLessons | 0),
                'LessonReview[CourseStartDate]': startDate, 'LessonReview[CourseEndDate]': endDate
            });
        } else {
            var params = $.param({
                'LessonSearch[showAllReviewLessons]': (showAllReviewLessons | 0),
            });
        }
        var url = "<?php echo Url::to(['lesson/review', 'LessonReview[courseId]' => $courseModel ? $courseModel->id : null, 
            'LessonReview[enrolmentIds]' => $model->enrolmentIds]); ?>&" + params;
        $.pjax.reload({url: url, container: "#review-lesson-listing", replace: false, timeout: 4000});  //Reload GridView
    });

    $(document).on('click', '.review-lesson-edit-button', function () {
        $.ajax({
            url: '<?= Url::to(['lesson/update-field']); ?>?id=' + $(this).parent().parent().data('key'),
            type: 'get',
            dataType: "json",
            success: function (response)
            {
                if (response.status)
                {
                    $('#modal-content').html(response.data);
                    $('#popup-modal').modal('show');
                    $('#popup-modal .modal-dialog').css({'width': '1000px'});
                    $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Lesson edit</h4>');
                    $('.modal-save').text('Apply');
                    $('.modal-save').show();
                    $('.modal-save-all').show();
                }
            }
        });
        return false;
    });

    $(document).on('click', '.modal-save', function() {
        if ($('#lesson-applycontext').length !== 0) {
            $('#lesson-applycontext').val($(this).val());
        }
    });

    $(document).on('modal-success', function(event, params) {
        var showAllReviewLessons = $('#lessonsearch-showallreviewlessons').is(":checked");
        var param = $.param({'LessonSearch[showAllReviewLessons]': (showAllReviewLessons | 0)});
        var url = "<?php echo Url::to(['lesson/review', 'LessonReview[courseId]' => $courseModel ? $courseModel->id : null, 
            'LessonReview[enrolmentIds]' => $model->enrolmentIds]); ?>&" + param;
        if ($('#review-lesson-listing').length !== 0) {
            $.pjax.reload({url: url, container: "#review-lesson-listing", replace: false, timeout: 4000, async: false});
        }
        if ($('#review-lesson-summary').length !== 0) {
            $.pjax.reload({url: url, container: "#review-lesson-summary", replace: false, timeout: 6000, async: false});
        }
        if ($("#confirm-button").length !== 0) {
            review.onEditableGridSuccess();
        }
        return false;
    });
    
</script>
