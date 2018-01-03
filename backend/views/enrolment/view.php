<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Tabs;
use common\models\LocationAvailability;

$this->title = $model->course->program->name;
$this->params['label'] = $this->render('_title', [
	'model' => $model,
]);
$this->params['action-button'] = Html::a('<i class="fa fa-trash-o"></i>', [
	'enrolment/delete', 'id' => $model->id
], [
		'id' => 'enrolment-delete-' . $model->id,
		'title' => Yii::t('yii', 'Delete'),
		'class' => 'enrolment-delete btn btn-box-tool',
	])?>
<script src="/plugins/bootbox/bootbox.min.js"></script>
<div id="enrolment-delete" style="display: none;" class="alert-danger alert fade in"></div>
<div id="enrolment-delete-success" style="display: none;" class="alert-success alert fade in"></div>
<div id="enrolment-enddate-alert" style="display: none;" class="alert-info alert fade in"></div>
<?= $this->render('_view-enrolment', [
    'model' => $model,
]);?>
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
    $logContent=$this->render('log/index', [
        'logDataProvider' => $logDataProvider,
    ]);
    $items = [
        [
            'label' => 'Lesson',
            'content' => $lessonContent,
            'options' => [
                'id' => 'lesson',
            ],
        ],
        [
            'label' => 'Payment Cycle',
            'content' => $noteContent,
            'options' => [
                'id' => 'payment-cycle',
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

    echo Tabs::widget([
		'items' => $items,
	]);
?>
</div>
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
	$(document).on('click', '.enrolment-delete', function () {
		var enrolmentId = '<?= $model->id;?>';
		 bootbox.confirm({ 
		message: "Are you sure you want to delete this enrolment?", 
		callback: function(result){
			if(result) {
				$('.bootbox').modal('hide');
			$.ajax({
				url: '<?= Url::to(['enrolment/delete']); ?>?id=' + enrolmentId,
				dataType: "json",
                data   : $(this).serialize(),
				success: function (response)
				{
					if (response.status)
					{
                        window.location.href = response.url;
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
});
</script>
