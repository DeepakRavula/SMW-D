<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use common\models\Program;
use common\models\Student;
use common\models\UserProfile;
use common\components\gridView\KartikGridView;
use common\models\Enrolment;
use yii\bootstrap\Modal;
use common\models\LocationAvailability;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\EnrolmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = 'Enrolments';
$this->params['action-button'] = Html::a(Yii::t('backend', '<i class="fa fa-plus f-s-18" aria-hidden="true"></i>'), '#', ['class' => 'new-enrol-btn']);

$this->params['show-all'] = $this->render('_button', [
	'searchModel' => $searchModel
]);
?>
<script src="/plugins/bootbox/bootbox.min.js"></script>
<link type="text/css" href="/plugins/fullcalendar-scheduler/lib/fullcalendar.min.css" rel='stylesheet' />
<link type="text/css" href="/plugins/fullcalendar-scheduler/lib/fullcalendar.print.min.css" rel='stylesheet' media='print' />
<script type="text/javascript" src="/plugins/fullcalendar-scheduler/lib/fullcalendar.min.js"></script>
<link type="text/css" href="/plugins/fullcalendar-scheduler/scheduler.css" rel="stylesheet">
<script type="text/javascript" src="/plugins/fullcalendar-scheduler/scheduler.js"></script>
<link type="text/css" href="/plugins/bootstrap-datepicker/bootstrap-datepicker.css" rel='stylesheet' />
<script type="text/javascript" src="/plugins/bootstrap-datepicker/bootstrap-datepicker.js"></script>
	<?php $columns = [
		[
            'attribute' => 'program',
                'label' => 'Program',
				'value' => function($data) {
					return $data->course->program->name;
				},
				'filterType'=>KartikGridView::FILTER_SELECT2,
				'filter'=>ArrayHelper::map(
					Program::find()->orderBy(['name' => SORT_ASC])
					->joinWith(['course' => function($query) {
						$query->joinWith(['enrolment'])
						->confirmed()
						->location(Yii::$app->session->get('location_id'));
					}])
					->asArray()->all(), 'id', 'name'), 
				'filterInputOptions'=>['placeholder'=>'Program'],
				'format'=>'raw',
                'filterWidgetOptions'=>[
					'pluginOptions'=>[
						'allowClear'=>true,
                        ]
                    ],
			],
			[
            'attribute' => 'student',
                'label' => 'Student',
				'value' => function($data) {
					return $data->student->fullName;
				},
				'filterType'=>KartikGridView::FILTER_SELECT2,
				'filter'=>ArrayHelper::map(Student::find()->orderBy(['first_name' => SORT_ASC])
					->joinWith(['enrolment' => function($query) {
						$query->joinWith(['course' => function($query) {
							$query->confirmed()
								->location(Yii::$app->session->get('location_id'));
						}]);
					}])
					->asArray()->all(), 'id', 'first_name'), 
				'filterWidgetOptions'=>[
					'options' => [
						'id' => 'student',
					],
                    'pluginOptions'=>[
						'allowClear'=>true,
                        ],
				],
				'filterInputOptions'=>['placeholder'=>'Student'],
				'format'=>'raw'
			],
			[
            'attribute' => 'teacher',
                'label' => 'Teacher',
				'value' => function($data) {
					return $data->course->teacher->publicIdentity;
				},
				'filterType'=>KartikGridView::FILTER_SELECT2,
				'filter'=>ArrayHelper::map(UserProfile::find()->orderBy(['firstname' => SORT_ASC])
					->joinWith(['courses' => function($query) {
						$query->joinWith('enrolment')
							->confirmed()
							->location(Yii::$app->session->get('location_id'));
					}])
					->asArray()->all(), 'user_id', 'firstname'), 
				'filterWidgetOptions'=>[
					'options' => [
						'id' => 'teacher',
					],
                    'pluginOptions'=>[
						'allowClear'=>true,
                        ],
					
				],
				'filterInputOptions'=>['placeholder'=>'Teacher'],
				'format'=>'raw'
			],
			[
            'attribute' => 'expirydate',
                'label' => 'Expiry Date',
				'format' => 'date',
				'value' => function($data) {
					return Yii::$app->formatter->asDate($data->course->endDate);
				},
				'contentOptions' => ['style' => 'width:200px'],
				'filterType'=>KartikGridView::FILTER_DATE,
				'filterWidgetOptions'=>[
					'pluginOptions'=>[
						'allowClear'=>true,
						'autoclose' => true,
						'format' => 'dd-mm-yyyy',
					],
				],
			],	
			[
			'class' => 'yii\grid\ActionColumn',
			'contentOptions' => ['style' => 'width:50px'],
			'template' => '{view}',
			'buttons' => [
				'view' => function ($url, $model) {
					$url = Url::to(['enrolment/view', 'id' => $model->id]);
					return Html::a('<i class="fa fa-eye"></i>', $url, [
						'title' => Yii::t('yii', 'View'),
						'class' => ['btn-primary btn-xs m-l-10']
					]);
				},
			]
        ], 
	]; ?>
	<?php
	echo KartikGridView::widget([
		'dataProvider' => $dataProvider,
        'filterModel'=>$searchModel,
		'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
		'rowOptions' => function ($model, $key, $index, $grid) use ($searchModel) {
			if ($model->isExpiring(Enrolment::ENROLMENT_EXPIRY)) {
				return ['class' => 'danger inactive'];
			}
		},
		'columns' => $columns,
		'pjax'=>true,
		'pjaxSettings' => [
		'neverTimeout' => true,
		'options' => [
			'id' => 'enrolment-listing',
		],
	],
	]);
	?>
<?php Modal::begin([
    'header' => '<h4 class="m-0">New Enrolment</h4>',
    'id' => 'reverse-enrol-modal',
]); ?>
<?= $this->render('_index');?>
<?php Modal::end(); ?>
<?php
    $locationId = \common\models\Location::findOne(['slug' => \Yii::$app->language])->id;
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
$(document).ready(function(){
	function loadCalendar() {
 		var date = $('#course-startdate').val();
        $('#reverse-enrolment-calendar').fullCalendar({
     		defaultDate: moment(date, 'DD-MM-YYYY', true).format('YYYY-MM-DD'),
            schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
             header: {
                 left: 'prev,next today',
                 center: 'title',
                 right: ''
             },
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
	$(document).on('click', '.step1-next', function() {
		if($('#course-programid').val() == "") {
			$('#new-enrolment-form').yiiActiveForm('updateAttribute', 'course-programid', ["Program cannot be blank"]);
		} else {
			$('#step-1, #step-3, #step-4').hide();
			$('#reverse-enrol-modal .modal-dialog').css({'width': '1000px'});
			$('#step-2').show();
			loadCalendar();
		}
		return false;
	});
	$(document).on('click', '.step2-next', function() {
		if($('#course-teacherid').val() == "") {
			$('#new-enrolment-form').yiiActiveForm('updateAttribute', 'course-teacherid', ["Teacher cannot be blank"]);
			
		}else if($('#courseschedule-day').val() == "") {
			$('#error-notification').html('Please choose the date/time in the calendar').fadeIn().delay(3000).fadeOut();
		} else {
			$('#step-1, #step-2, #step-4').hide();
			$('#step-3').show();
			$('#reverse-enrol-modal .modal-dialog').css({'width': '600px'});
		}
		return false;
	});
	$(document).on('click', '.step2-back', function() {
		$('#step-3, #step-2, #step-4').hide();
		$('#step-1').show();
 		$('#reverse-enrol-modal .modal-dialog').css({'width': '600px'});
		return false;
	});
	$(document).on('click', '.step3-next', function() {
		if($('#userprofile-firstname').val() == "") {
			$('#new-enrolment-form').yiiActiveForm('updateAttribute', 'userprofile-firstname', ["Firstname cannot be blank"]);
		} else if($('#userprofile-lastname').val() == "") {
			$('#new-enrolment-form').yiiActiveForm('updateAttribute', 'userprofile-lastname', ["Lastname cannot be blank"]);
		} else if($('#userphone-number').val() == "") {
			$('#new-enrolment-form').yiiActiveForm('updateAttribute', 'userphone-number', ["Number cannot be blank"]);
		} else if($('#useraddress-address').val() == "") {
			$('#new-enrolment-form').yiiActiveForm('updateAttribute', 'useraddress-address', ["Address cannot be blank"]);
		} else {
			$('#step-1, #step-2, #step-3').hide();
			$('#step-4').show();
			$('#reverse-enrol-modal .modal-dialog').css({'width': '400px'});
			var lastName = $('#userprofile-lastname').val();
			$('#student-last_name').val(lastName);
		}
		return false;
	});
	$(document).on('click', '.step3-back', function() {
		$('#step-3, #step-1, #step-4').hide();
		loadCalendar();
		$('#step-2').show();
 		$('#reverse-enrol-modal .modal-dialog').css({'width': '1000px'});
		return false;
	});
	$(document).on('click', '.step4-back', function() {
		$('#step-2, #step-3, #step-4').hide();
		$('#step-3').show();
 		$('#reverse-enrol-modal .modal-dialog').css({'width': '600px'});
		return false;
	});
	$(document).on('click', '.new-enrol-btn', function() {
		$('#step-2,#step-3, #step-4').hide();
		$('#step-1').show();
 		$('#reverse-enrol-modal .modal-dialog').css({'width': '600px'});
		$('#reverse-enrol-modal').modal('show');
        return false;	
	});
	$(document).on('click', '.new-enrol-cancel', function() {
		$('#reverse-enrol-modal').modal('hide');
        return false;	
	});
	 $(document).on('beforeSubmit', '#new-enrolment-form', function(){
        $.ajax({
            url    : '<?= Url::to(['enrolment/add']); ?>',
            type   : 'post',
            dataType: "json",
            data: $(this).serialize(),
            success: function(response)
            {
            }
        });
        return false;
    });
  $("#enrolmentsearch-showallenrolments").on("change", function() {
      var showAllEnrolments = $(this).is(":checked");
      var url = "<?php echo Url::to(['enrolment/index']); ?>?EnrolmentSearch[showAllEnrolments]=" + (showAllEnrolments | 0);
      $.pjax.reload({url:url,container:"#enrolment-listing",replace:false,  timeout: 4000});  //Reload GridView
    });
});
</script>