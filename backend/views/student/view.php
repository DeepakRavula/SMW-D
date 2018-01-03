<?php

use yii\helpers\Html;
use yii\bootstrap\Tabs;
use common\models\Vacation;
use common\models\ExamResult;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use common\models\Note;
use kartik\select2\Select2Asset;
use kartik\daterange\DateRangePickerAsset;
use yii\widgets\Pjax;
use common\models\LocationAvailability;

Select2Asset::register($this);
DateRangePickerAsset::register($this);              

/* @var $this yii\web\View */
/* @var $model common\models\Student */
$this->title = $model->fullName;
$this->params['label'] = $this->render('_title', [
	'model' => $model,
]);?>
<div id="enrolment-delete" style="display: none;" class="alert-danger alert fade in"></div>
<div id="enrolment-delete-success" style="display: none;" class="alert-success alert fade in"></div>
<script src="/plugins/bootbox/bootbox.min.js"></script>
<link type="text/css" href="/plugins/fullcalendar-scheduler/lib/fullcalendar.min.css" rel='stylesheet' />
<link type="text/css" href="/plugins/fullcalendar-scheduler/lib/fullcalendar.print.min.css" rel='stylesheet' media='print' />
<script type="text/javascript" src="/plugins/fullcalendar-scheduler/lib/fullcalendar.min.js"></script>
<link type="text/css" href="/plugins/fullcalendar-scheduler/scheduler.css" rel="stylesheet">
<script type="text/javascript" src="/plugins/fullcalendar-scheduler/scheduler.js"></script>
<link type="text/css" href="/plugins/bootstrap-datepicker/bootstrap-datepicker.css" rel='stylesheet' />
<script type="text/javascript" src="/plugins/bootstrap-datepicker/bootstrap-datepicker.js"></script>
<div class="row">
	<?php
	echo $this->render('_profile', [
		'model' => $model,
	]);
	?>
</div>
<div class="row">
<?php Pjax::begin(['id' => 'enrolment-list']);?>
	<?php
	echo $this->render('enrolment/view', [
		'model' => $model,
		'enrolmentDataProvider' => $enrolmentDataProvider,
	]);
	?>
<?php Pjax::end();?>
</div>
<div class="row">
	<?php
	echo $this->render('exam-result/view', [
		'model' => new ExamResult(),
		'studentModel' => $model,
		'examResultDataProvider' => $examResultDataProvider
	]);
	?>
</div>

<div class="nav-tabs-custom">
		<?php
		$lessonContent = $this->render('_lesson', [
			'lessonDataProvider' => $lessonDataProvider,
			'model' => $model,
            'allEnrolments' => $allEnrolments
		]);

		$unscheduledLessonContent = $this->render('_unscheduledLesson', [
			'dataProvider' => $unscheduledLessonDataProvider,
		]);

		$vacationContent = $this->render('vacation/_index', [
			'model' => new Vacation(),
			'studentModel' => $model,
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

		if(!empty($model->studentCsv)) {
			$csvContent = $this->render('csv', [
				'model' => $model->studentCsv,
				'studentModel' => $model
			]);
		}
		$items = [
				[
				'label' => 'Lessons',
				'content' => $lessonContent,
				'options' => [
					'id' => 'lesson',
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
				'label' => 'Vacations',
				'content' => $vacationContent,
				'options' => [
					'id' => 'vacation',
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
		if(!empty($model->studentCsv)) {
			array_push($items, [
				'label' => 'CSV',
				'content' => $csvContent,
				'options' => [
					'id' => 'csv',
				],	
			]);
		}
		?>
		<?php
		echo Tabs::widget([
		'items' => $items
		]);
		?>
		<div class="clearfix"></div>
</div>

<?php Modal::begin([
    'header' => '<h4 class="m-0">Student Merge</h4>',
    'id' => 'student-merge-modal',
]); ?>
<div id="student-merge-content"></div>
<?php Modal::end(); ?>
<?php
    $locationId = \common\models\Location::findOne(['slug' => \Yii::$app->location])->id;
    $minLocationAvailability = LocationAvailability::find()
        ->where(['locationId' => $locationId])
        ->orderBy(['fromTime' => SORT_ASC])
        ->one();
    $maxLocationAvailability = LocationAvailability::find()
        ->where(['locationId' => $locationId])
        ->orderBy(['toTime' => SORT_DESC])
        ->one();
    $from_time = (new \DateTime($minLocationAvailability->fromTime))->format('H:i:s');
    $to_time = (new \DateTime($maxLocationAvailability->toTime))->format('H:i:s');
?>
<script>
    $(document).ready(function () {
	function loadCalendar() {
 		var date = $('#course-startdate').val();
        $('#enrolment-calendar').fullCalendar({
     		defaultDate: moment(date, 'DD-MM-YYYY', true).format('YYYY-MM-DD'),
            schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
             header: {
                 left: 'prev,next today',
                 center: 'title',
                 right: ''
             },
			firstDay :1,
             allDaySlot: false,
             slotDuration: '00:15:00',
             titleFormat: 'DD-MMM-YYYY, dddd',
             defaultView: 'agendaWeek',
             minTime: "<?php echo $from_time; ?>",
             maxTime: "<?php echo $to_time; ?>",
             selectConstraint: 'businessHours',
             eventConstraint: 'businessHours',
             businessHours: [],
             allowCalEventOverlap: true,
             overlapEventsSeparate: true,
             events: [],
     	});
	}
		$('#step-2, #step-1').hide();
        $(document).on('click', '#add-private-enrol', function () {
			$('#step-1').show();
			$('#step-2').hide();
            $('#private-enrol-modal').modal('show');
 			$('#private-enrol-modal .modal-dialog').css({'width': '600px'});
            return false;
		});
		$(document).on('click', '.step1-next', function () {
			if($('#course-programid').val() == "") {
				$('#enrolment-form').yiiActiveForm('updateAttribute', 'course-programid', ["Program cannot be blank"]);
			} else {
				$('#step-1').hide();
				$('#step-2').show();
				loadCalendar();
				$('#private-enrol-modal .modal-dialog').css({'width': '1000px'});
				return false;
			}
		});
		$(document).on('click', '.step2-back', function () {
			$('#step-1').show();
			$('#step-2').hide();
 			$('#private-enrol-modal .modal-dialog').css({'width': '600px'});
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
			var id = '<?= $model->id;?>';
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
		$(document).on('click', '.private-enrol-cancel', function() {
			$('#private-enrol-modal').modal('hide');
			return false;
		});
        $(document).on('click', '.merge-cancel', function () {
            $('#student-merge-modal').modal('hide');
            return false;
        });
        $.fn.modal.Constructor.prototype.enforceFocus = function() {};
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
                        $('#student-merge-content').html(response.data);
                        $('#student-merge-modal').modal('show');
                    }
                }
            });
            return false;
        });
     $(document).on('click', '.group-enrol-btn', function() {
         $('#course-spinner').show();
         var courseId=$(this).attr('data-key');
              var params = $.param({'courseId': courseId });
         $.ajax({
             url    : '<?= Url::to(['enrolment/group' ,'studentId' => $model->id]); ?>&' + params,
             type: 'post',
             success: function(response) {
                 if (response.status) {
                     $('#course-spinner').hide();
                      $.pjax.reload({container: "#enrolment-grid", replace: false, async: false, timeout: 6000});
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
 
    $(document).on('beforeSubmit', '#student-merge-form', function () {
            $.ajax({
                url    : '<?= Url::to(['student/merge', 'id' => $model->id]); ?>',
                type   : 'post',
                dataType: "json",
                data   : $(this).serialize(),
                success: function(response)
                {
                    if(response.status) {
                        location.reload();
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
        $(document).on('click', '.exam-result-cancel-button', function () {
            $('#new-exam-result-modal').modal('hide');
            return false;
        });
		$(document).on('click', '.extra-lesson-cancel-button', function () {
            $('#new-lesson-modal').modal('hide');
            return false;
        });
        $(document).on("click", ".add-new-exam-result,#student-exam-result-listing tbody > tr", function() {
		var examResultId = $(this).data('key');
        var studentId=<?= $model->id ?>;
            if (examResultId === undefined) {
                var customUrl = '<?= Url::to(['exam-result/create']); ?>?studentId='+studentId;
            } else {
                var customUrl = '<?= Url::to(['exam-result/update']); ?>?id=' + examResultId;
            }
            $.ajax({
                url    : customUrl,
                type: 'get',
                dataType: "json",
                success: function (response)
                {
                    if (response.status)
                    {
                        $('#new-exam-result-modal .modal-body').html(response.data);
                        $('#new-exam-result-modal').modal('show');
                    } else {
                        $('#lesson-form').yiiActiveForm('updateMessages',
                                response.errors
                                , true);
                    }
                }
            });
            
		return false;
	});
		
$(document).on('click', '.evaluation-delete', function () {
		var examResultId = $('#examresult-id').val();
		 bootbox.confirm({ 
  			message: "Are you sure you want to delete this evaluation?", 
  			callback: function(result){
				if(result) {
					$('.bootbox').modal('hide');
				$.ajax({
					url: '<?= Url::to(['exam-result/delete']); ?>?id=' + examResultId,
					type: 'post',
					success: function (response)
					{
						if (response.status)
						{
                            $('#new-exam-result-modal').modal('hide');
							$.pjax.reload({container: '#student-exam-result-listing', timeout: 6000, async:false});
							$.pjax.reload({container: '#student-log', timeout: 6000, async:false});
						} else {
							$('#evaluation-delete').html('You are not allowed to delete this evaluation.').fadeIn().delay(3000).fadeOut();
						}
					}
				});
				return false;	
			}
			}
		});	
		return false;
        });
		$(document).on('click', '.enrolment-delete', function () {
		var enrolmentId = $(this).parent().parent().data('key');
		 bootbox.confirm({ 
  			message: "Are you sure you want to delete this enrolment?", 
  			callback: function(result){
				if(result) {
					$('.bootbox').modal('hide');
				$.ajax({
					url: '<?= Url::to(['enrolment/delete']); ?>?id=' + enrolmentId,
					type: 'post',
					success: function (response)
					{
						if (response.status)
						{
							$.pjax.reload({container: '#enrolment-grid', skipOuterContainers:true, timeout:6000});
						} else {
							$('#enrolment-delete').html('You are not allowed to delete this enrolment.').fadeIn().delay(3000).fadeOut();
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
        $(document).on('beforeSubmit', '#exam-result-form', function (e) {
        $.ajax({
                url    : $(this).attr('action'),
                type: 'post',
                dataType: "json",
                data: $(this).serialize(),
                success: function (response)
                {
                    if (response.status)
                    {
                        $.pjax.reload({container: '#student-exam-result-listing', timeout: 6000, async:false});
                        $.pjax.reload({container: '#student-log', timeout: 6000, async:false});
                        $('#new-exam-result-modal').modal('hide');
                    } else
                    {
                        $('#exam-result-form').yiiActiveForm('updateMessages',
                                response.errors
                                , true);
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
                url: '<?= Url::to(['note/create', 'instanceId' => $model->id, 'instanceType' => Note::INSTANCE_TYPE_STUDENT]); ?>',
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
        	$('#student-profile-modal .modal-dialog').css({'width': '400px'});
			$('#student-profile-modal').modal('show');
			return false;
		});
		$(document).on('click', '.student-profile-cancel-button', function () {
			$('#student-profile-modal').modal('hide');
		});
		
		$(document).on('beforeSubmit', '#student-form', function (e) {
            $.ajax({
                url: $(this).attr('action'),
                type: 'post',
                dataType: "json",
                data: $(this).serialize(),
                success: function (response)
                {
                    if (response.status)
                    {
                        $.pjax.reload({container: '#student-profile', timeout: 6000, async:false});
                        $.pjax.reload({container: '#student-log', timeout: 6000, async:false});
                        $('#student-profile-modal').modal('hide');
                    } else {
						$('#student-form').yiiActiveForm('updateMessages',
                            response.errors, true);	
					}
                }
    	});
		return false;
    });
});
</script>