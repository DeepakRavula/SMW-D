<?php

use backend\models\search\LessonSearch;
use common\components\gridView\KartikGridView;
use common\models\Lesson;
use kartik\daterange\DateRangePicker;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\widgets\Pjax;
use Carbon\Carbon;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>

<div id="index-success-notification" style="display:none;" class="alert-success alert fade in"></div>
<div id="index-error-notification" style="display:none;" class="alert-danger alert fade in"></div>

<div id="loader" class="spinner" style="display:none">
    <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
    <span class="sr-only">Loading...</span>
</div>
<div class="grid-row-open">
    <?php Pjax::begin(['id' => 'lesson-index', 'timeout' => 25000]);?>
    <?php $columns = [
    [
        'class' => '\kartik\grid\CheckboxColumn',
        'mergeHeader' => false,
    ],
    [
        'label' => 'Date',
        'attribute' => 'dateRange',
        'filter' => DateRangePicker::widget([
            'model' => $searchModel,
            'convertFormat' => true,
            'initRangeExpr' => true,
            'attribute' => 'dateRange',
            'options' => [
                'class' => 'form-control',
                'readOnly' => true,
            ],
            'pluginOptions' => [
                'autoApply' => true,
                'ranges' => [
                    Yii::t('kvdrp', 'All') => ["moment('<?= $firstLessonDate ?>', 'MMM D,YYYY').format('MMM D, YYYY')", "moment('<?= $lastLessonDate ?>', 'MMM D,YYYY').format('MMM D, YYYY')"],
                    Yii::t('kvdrp', 'Today') => ["moment().startOf('day')", "moment()"],
                    Yii::t('kvdrp', 'Tomorrow') => ["moment().startOf('day').add(1,'days')", "moment().endOf('day').add(1,'days')"],
                    Yii::t('kvdrp', 'Next {n} Days', ['n' => 7]) => ["moment().startOf('day')", "moment().endOf('day').add(6, 'days')"],
                    Yii::t('kvdrp', 'Next {n} Days', ['n' => 30]) => ["moment().startOf('day')", "moment().endOf('day').add(29, 'days')"],
                ],
                'locale' => [
                    'format' => 'M d, Y',
                ],
                'opens' => 'right',
            ],
        ]) ,
        'value' => function ($data) {
            $date = Yii::$app->formatter->asDate($data->date);
            $lessonTime = (new \DateTime($data->date))->format('H:i:s');

            return !empty($date) ? $date . ' @ ' . Yii::$app->formatter->asTime($lessonTime) : null;
        },
    ],
    [
        'label' => 'Student',
        'attribute' => 'student',
        'value' => function ($data) {
            return !empty($data->course->enrolment->student->fullName) ? $data->course->enrolment->student->fullName : null;
        },
    ],
    [
        'label' => 'Program',
        'attribute' => 'program',
        'value' => function ($data) {
            return $data->course->program->name;
        },
    ],
    [
        'label' => 'Teacher',
        'attribute' => 'teacher',
        'value' => function ($data) {
            return $data->teacher->publicIdentity;
        },
    ],
    [
        'label' => 'Duration',
        'attribute' => 'duration',
        'value' => function ($data) {
            $lessonDuration = (new \DateTime($data->duration))->format('H:i');
            return $lessonDuration;
        },
    ],     
];
array_push($columns, [
    'label' => 'Online',
    'attribute' => 'isOnline',
    'filter' => LessonSearch::lessonClassType(),
    'filterWidgetOptions' => [
        'options' => [
            'id' => 'lesson-online-status',
        ],
    ],
    'value' => function ($data) {
        $lessonType = ($data->is_online ?? 0) == 0 ? 'No' : 'Yes';
        return  $lessonType;
    },
]);
array_push($columns, [
    'label' => 'Status',
    'attribute' => 'lessonStatus',
    'filter' => LessonSearch::lessonStatuses(),
    'filterWidgetOptions' => [
        'options' => [
            'id' => 'lesson-index-status',
        ],
    ],
    'value' => function ($data) {
        $status = null;
        if (!empty($data->status)) {
            return $data->getStatus();
        }
        return $status;
    },
]);
array_push($columns,
     [
        'label' => 'Payment',
        'attribute' => 'owing',
        'attribute' => 'owingStatus',
        'filter'=> LessonSearch::owingStatuses(),
        'contentOptions' => function ($data) {
            $highLightClass = 'text-right';
            if ($data->hasInvoice()) {
                if ($data->invoice->isOwing()) {
                    $highLightClass .= ' danger';
                }
            } else if (($data->privateLesson->balance ?? 0) > 0.09) {
                $highLightClass .= ' danger';
            }
            return ['class' => $highLightClass];
        },
        'headerOptions' => ['class' => 'text-right', 'style' => 'width:10%'],
        'value' => function ($data) {
            if ($data->hasInvoice()) {
                $owingAmount = $data->invoice->balance > 0.09 ? 'Owing' : 'Paid';
            } else {
                $owingAmount = ($data->privateLesson->balance ?? 0) > 0.09 ? 'Owing' : 'Paid';

            }
            return $owingAmount;
        },
    ]
);

array_push($columns, [
    'label' => 'Price',
    'attribute' => 'price',
    'contentOptions' => ['class' => 'text-right dollar'],
    'headerOptions' => ['class' => 'text-right'],
    'value' => function ($data) {
        return number_format($data->privateLesson->total ?? 0, 2);
    },
]);

if ((int) $searchModel->type === Lesson::TYPE_GROUP_LESSON) {
    array_shift($columns);
}
?>
    <?=KartikGridView::widget([
    'dataProvider' => $dataProvider,
    'options' => ['id' => 'lesson-index-1'],
    'filterModel' => $searchModel,
    'summary' => "Showing {begin} - {end} of {totalCount} items",
    'filterUrl' => Url::to(['lesson/index', 'LessonSearch[type]' => true, 'LessonSearch[showAll]' => $searchModel->showAll]),
    'rowOptions' => function ($model, $key, $index, $grid) {
        $url = Url::to(['lesson/view', 'id' => $model->id]);

        return ['data-url' => $url];
    },
    'tableOptions' => ['class' => 'table table-bordered'],
    'headerRowOptions' => ['class' => 'bg-light-gray'],
    'columns' => $columns,
    'toolbar' => [
        ['content' =>  $this->render('_action-menu', [
            'searchModel' => $searchModel]),
        ],
        ['content' =>  $this->render('_show-all-button-new', ['searchModel' => $searchModel]),
        ],
        '{export}',
        '{toggleData}'
    ],
    'export' => [
        'fontAwesome' => true,
    ],
    'panel' => [
        'type' => GridView::TYPE_DEFAULT,
        'heading' => 'Private Lessons'
    ],
]);?>
	<?php Pjax::end();?>

<?php Modal::begin([
    'header' => '<h4 class="m-0">Substitute Teacher</h4>',
    'id' => 'teacher-substitute-modal',
]);?>
<div id="teacher-substitute-content"></div>
<?php Modal::end();?>
</div>

<script>
      $(document).ready(function () {
        

        function initialLoad() {
            $("input[name*='LessonSearch[lessonStatus]").addClass('lesson-status');
            var showAll = $('#lessonsearch-showall').is(":checked");
            if (showAll == true) {
                var student = $("input[name*='LessonSearch[student]").val();
                var program = $("input[name*='LessonSearch[program]").val();
                var teacher = $("input[name*='LessonSearch[teacher]").val();
                var dateRange = $("input[name*='LessonSearch[dateRange]").val();
                var lessonStatus = $("select[name*='LessonSearch[lessonStatus]").val();
                var params = $.param({'LessonSearch[student]':student, 'LessonSearch[program]':program, 'LessonSearch[teacher]':teacher, 'LessonSearch[dateRange]': dateRange, 'LessonSearch[type]': <?=Lesson::TYPE_PRIVATE_LESSON?>,'LessonSearch[showAll]': (showAll | 0), 'LessonSearch[lessonStatus]': lessonStatus });
                var url = "<?=Url::to(['lesson/index']);?>?"+params;
                $.pjax.reload({url: url, container: "#lesson-index", replace: false, timeout: 25000});  
                bulkAction.setAction();
            }
        }

        initialLoad();
    });

    $(document).off('change', '#lesson-index-1 .select-on-check-all, input[name="selection[]"]').on('change', '#lesson-index-1 .select-on-check-all, input[name="selection[]"]', function () {
        bulkAction.setAction();
        return false;
    });

    $(document).on('modal-success', function(event, params) {
        if (!$.isEmptyObject(params.url)) {
            var dateRange = $("input[name*='LessonSearch[dateRange]").val();
            var params = $.param({'LessonSearch[dateRange]': dateRange});
            var url = "<?=Url::to(['lesson/index']);?>?"+params;
            window.location.href = url;
        } else if(params.status) {
            // $.pjax.reload({container: "#lesson-index-1",timeout: 6000, async:false});
            if (params.message) {
                $('#popup-modal').modal('hide');
                $('#index-success-notification').text(params.message).fadeIn().delay(5000).fadeOut();
            }
            var dateRange = $("input[name*='LessonSearch[dateRange]").val();
            var params = $.param({'LessonSearch[dateRange]': dateRange});
            var url = "<?=Url::to(['lesson/index']);?>?"+params;
            if (!params.reshedule) {
                window.location.href = url;
            } else {
                setTimeout(() => {
                    window.location.href = url;
                }, 4000);
            }
        }
        return false;
    });

    $(document).on('modal-error', function(event, params) {
        if (params.error) {
            $('#popup-modal').modal('hide');
            $('#index-error-notification').text(params.error).fadeIn().delay(5000).fadeOut();
        }
        return false;
    });

    $(document).off('change', '#lessonsearch-showall').on('change', '#lessonsearch-showall', function(){
        var showAll = $(this).is(":checked");
        var student = $("input[name*='LessonSearch[student]").val();
        var program = $("input[name*='LessonSearch[program]").val();
        var teacher = $("input[name*='LessonSearch[teacher]").val();
        var dateRange = $("input[name*='LessonSearch[dateRange]").val();
        var lessonStatus = $("select[name*='LessonSearch[lessonStatus]").val();
        var params = $.param({'LessonSearch[student]':student, 'LessonSearch[program]':program, 'LessonSearch[teacher]':teacher, 'LessonSearch[dateRange]': dateRange, 'LessonSearch[type]': <?=Lesson::TYPE_PRIVATE_LESSON?>,'LessonSearch[showAll]': (showAll | 0), 'LessonSearch[lessonStatus]': lessonStatus });
        var url = "<?=Url::to(['lesson/index']);?>?"+params;
        $.pjax.reload({url: url, container: "#lesson-index", replace: false, timeout: 25000});  //Reload GridView
    });

    $(document).off('click', '.remove-button').on('click', '.remove-button', function() {
        var dateRange = $("#lessonsearch-daterange").val();
        if (!$.isEmptyObject(dateRange)) {
            $("#lessonsearch1-daterange").val('').trigger('change');
        }
    });

    $(document).off('click', '#substitute-teacher').on('click', '#substitute-teacher', function(){
        var lessonIds = $('#lesson-index-1').yiiGridView('getSelectedRows');
        if ($.isEmptyObject(lessonIds)) {
            $('#index-error-notification').html("Choose any lessons to substitute teacher").fadeIn().delay(5000).fadeOut();
        } else {
            var params = $.param({ ids: lessonIds });
            $.ajax({
                url    : '<?=Url::to(['teacher-substitute/index'])?>?' +params,
                type   : 'get',
                success: function(response)
                {
                    if (response.status) {
                        $('#teacher-substitute-modal').modal('show');
                        $('#teacher-substitute-modal .modal-dialog').css({'width': '1000px'});
                        $('#teacher-substitute-content').html(response.data);
                    } else {
                        $('#index-error-notification').html("Choose lessons with same teacher").fadeIn().delay(5000).fadeOut();
                    }
                }
            });
            return false;
        }
    });

    $(document).off('click', '#lesson-discount').on('click', '#lesson-discount', function(){
        var lessonIds = $('#lesson-index-1').yiiGridView('getSelectedRows');
        if ($.isEmptyObject(lessonIds)) {
            $('#index-error-notification').html("Choose any lessons to edit discount").fadeIn().delay(5000).fadeOut();
        } else {
            var params = $.param({ 'LessonDiscount[ids]': lessonIds });
            $.ajax({
                url    : '<?=Url::to(['private-lesson/apply-discount'])?>?' +params,
                type   : 'get',
                success: function(response)
                {
                    if (response.status) {
                        $('#modal-content').html(response.data);
                        $('#popup-modal').modal('show');
                    } else {
                        if (response.message) {
                            $('#index-error-notification').text(response.message).fadeIn().delay(5000).fadeOut();
                        }
                    }
                }
            });
            return false;
        }
    });

    $(document).off('change', '#lesson-index-1 .select-on-check-all, input[name="selection[]"]').on('change', '#lesson-index-1 .select-on-check-all, input[name="selection[]"]', function () {
        bulkAction.setAction();
        return false;
    });

    var bulkAction = {
        setAction: function() {
            var lessonIds = $('#lesson-index-1').yiiGridView('getSelectedRows');
            if ($.isEmptyObject(lessonIds)) {
                $('#substitute-teacher').addClass('multiselect-disable');
                $('#lesson-discount').addClass('multiselect-disable');
                $('#lesson-delete').addClass('multiselect-disable');
                $('#lesson-duration-edit').addClass('multiselect-disable');
                $('#lesson-classroom-edit').addClass('multiselect-disable');
                $('#lesson-online-edit').addClass('multiselect-disable');
                $('#email-multi-customer').addClass('multiselect-disable');
                $('#lesson-unschedule').addClass('multiselect-disable');
                $('#lesson-reschedule').removeClass('multiselect-disable');
                $('#generate-invoice').removeClass('multiselect-disable');
            } else {
                $('#substitute-teacher').removeClass('multiselect-disable');
                $('#lesson-discount').removeClass('multiselect-disable');
                $('#lesson-delete').removeClass('multiselect-disable');
                $('#lesson-duration-edit').removeClass('multiselect-disable');
                $('#lesson-classroom-edit').removeClass('multiselect-disable');
                $('#lesson-online-edit').removeClass('multiselect-disable');
                $('#email-multi-customer').removeClass('multiselect-disable');
                $('#lesson-unschedule').removeClass('multiselect-disable');
                $('#lesson-reschedule').removeClass('multiselect-disable');
                $('#generate-invoice').removeClass('multiselect-disable');
            }
            return false;
        }
    };

    $(document).off('click', '#lesson-delete').on('click', '#lesson-delete', function(){
        var lessonIds = $('#lesson-index-1').yiiGridView('getSelectedRows');
        if ($.isEmptyObject(lessonIds)) {
            $('#index-error-notification').html("Choose any lessons to delete").fadeIn().delay(5000).fadeOut();
        } else {
            var params = $.param({ 'PrivateLesson[ids]': lessonIds, 'PrivateLesson[isBulk]': true });
            bootbox.confirm({
                message: "Are you sure you want to delete this lesson?",
                callback: function(result) {
                    if(result) {
                        $('.bootbox').modal('hide');
                        $.ajax({
                            url    : '<?=Url::to(['private-lesson/delete'])?>?' +params,
                            type   : 'post',
                            success: function(response)
                            {
                                if (response.status) {
                                    if (response.message) {
                                        $('#index-success-notification').text(response.message).fadeIn().delay(5000).fadeOut();
                                        $.pjax.reload({container: "#lesson-index", replace: false, async: false, timeout: 25000});
                                    }
                                } else {
                                    if (response.message) {
                                        $('#index-error-notification').text(response.message).fadeIn().delay(5000).fadeOut();
                                    }
                                }
                            }
                        });
                    }
                }
            });
        }
        return false;
    });
    $(document).off('click', '#lesson-duration-edit').on('click', '#lesson-duration-edit', function(){
        var lessonIds = $('#lesson-index-1').yiiGridView('getSelectedRows');
        if ($.isEmptyObject(lessonIds)) {
            $('#index-error-notification').html("Choose any lessons to edit duration").fadeIn().delay(5000).fadeOut();
        } else {
            var params = $.param({ 'PrivateLesson[ids]': lessonIds, 'PrivateLesson[isBulk]': true });
                        $.ajax({
                            url    : '<?=Url::to(['private-lesson/edit-duration'])?>?' +params,
                            type   : 'post',
                            success: function(response)
                            {
                                if (response.status) {
                                        $('#modal-content').html(response.data);
                                        $('#popup-modal').modal('show');
                                    }

                                else {
                                    if (response.message) {
                                        $('#index-error-notification').text(response.message).fadeIn().delay(5000).fadeOut();
                                    }
                                }
                            }
                        });

        }
        return false;
    });

     $(document).off('click', '#lesson-classroom-edit').on('click', '#lesson-classroom-edit', function(){
        var lessonIds = $('#lesson-index-1').yiiGridView('getSelectedRows');
        var params = $.param({ 'EditClassroom[lessonIds]': lessonIds});
                    $.ajax({
                        url    : '<?=Url::to(['private-lesson/edit-classroom'])?>?' +params,
                        type   : 'post',
                        success: function(response)
                        {
                            if (response.status) {
                                    $('#modal-content').html(response.data);
                                    $('#popup-modal').modal('show');
                                }
                            else {
                                if (response.message) {
                                    $('#index-error-notification').text(response.message).fadeIn().delay(5000).fadeOut();
                                }
                                if (response.error) {
                                    $('#index-error-notification').text(response.error).fadeIn().delay(5000).fadeOut();
                                }
                            }
                        }
                    });
        return false;
    });
    $(document).off('click', '#lesson-online-edit').on('click', '#lesson-online-edit', function(){
        var lessonIds = $('#lesson-index-1').yiiGridView('getSelectedRows');
        var params = $.param({ 'PrivateLesson[lessonIds]': lessonIds});
                    $.ajax({
                        url    : '<?=Url::to(['private-lesson/edit-online-type'])?>?' +params,
                        type   : 'post',
                        success: function(response)
                        {
                            if (response.status) {
                                    $('#modal-content').html(response.data);
                                    $('#popup-modal').modal('show');
                                }
                            else {
                                if (response.message) {
                                    $('#index-error-notification').text(response.message).fadeIn().delay(5000).fadeOut();
                                }
                                if (response.error) {
                                    $('#index-error-notification').text(response.error).fadeIn().delay(5000).fadeOut();
                                }
                            }
                        }
                    });
        return false;
    });

   $(document).off('click', '#email-multi-customer').on('click', '#email-multi-customer', function(){
        var lessonIds = $('#lesson-index-1').yiiGridView('getSelectedRows');
        if (!$.isEmptyObject(lessonIds)) {
            var params = $.param({ 'EmailMultiCustomer[lessonIds]': lessonIds});
                    $.ajax({
                        url    : '<?=Url::to(['email-multi-customer/email-multi-customer'])?>?' +params,
                        type   : 'post',
                        success: function(response)
                        {
                            if (response.status) {
                                    $('#modal-content').html(response.data);
                                    $('#popup-modal').modal('show');
                                }
                            else {
                                if (response.message) {
                                    $('#index-error-notification').text(response.message).fadeIn().delay(5000).fadeOut();
                                }
                                if (response.error) {
                                    $('#index-error-notification').text(response.error).fadeIn().delay(5000).fadeOut();
                                }
                            }
                        }
                    });
        } else {
            $('#index-error-notification').text('Select Any Lessons').fadeIn().delay(5000).fadeOut();
        }
        return false;
    });

    $(document).off('click', '#lesson-unschedule').on('click', '#lesson-unschedule', function(){
        var lessonIds = $('#lesson-index-1').yiiGridView('getSelectedRows');
        if (!$.isEmptyObject(lessonIds)) {
            var params = $.param({ 'UnscheduleLesson[lessonIds]': lessonIds, 'UnscheduleLesson[isBulk]': true});
            $('#menu-shown').hide();
               bootbox.confirm({
                message: "Are you sure you want to unschedule?",
                callback: function(result) {
                    if(result) {
                        $('.bootbox').modal('hide');
                        $.ajax({
                        url    : '<?=Url::to(['unscheduled-lesson/reason-to-unschedule'])?>?' +params,
                        type   : 'get',
                        success: function(response)
                        {
                            if (response.status) {
                                $('#modal-content').html(response.data);
                                $('#popup-modal').modal('show');
                                }
                        }
                    });                  
        }
                }
               });
        } else {
            $('#index-error-notification').text('Select Any Lessons').fadeIn().delay(5000).fadeOut();
        }
        return false;
    });

    $(document).off('click', '#lesson-reschedule').on('click', '#lesson-reschedule', function(){
        var lessonIds = $('#lesson-index-1').yiiGridView('getSelectedRows');
        var params = $.param({ 'PrivateLesson[lessonIds]': lessonIds});
                    $.ajax({
                        url    : '<?=Url::to(['private-lesson/bulk-reschedule'])?>?' +params,
                        type   : 'post',
                        success: function(response)
                        {
                            if (response.status) {
                                    $('#modal-content').html(response.data);
                                    $('#popup-modal').modal('show');
                                }
                            else {
                                if (response.message) {
                                    $('#index-error-notification').text(response.message).fadeIn().delay(5000).fadeOut();
                                }
                                if (response.error) {
                                    $('#index-error-notification').text(response.error).fadeIn().delay(5000).fadeOut();
                                }
                            }
                        }
                    });
        return false;
    });


    $(document).off('click', '#generate-invoice').on('click', '#generate-invoice', function(){
        var lessonIds = $('#lesson-index-1').yiiGridView('getSelectedRows');
        var params = $.param({ 'PrivateLesson[lessonIds]': lessonIds});
                    $.ajax({
                        url    : '<?=Url::to(['private-lesson/generate-invoice'])?>?' +params,
                        type   : 'post',
                        success: function(response)
                        {
                            if (response.message) {
                                $('#index-error-notification').text(response.message).fadeIn().delay(5000).fadeOut();
                            }
                            if (response.error) {
                                $('#index-error-notification').text(response.error).fadeIn().delay(5000).fadeOut();
                            }
                        }
                    });
        return false;
    });

</script>