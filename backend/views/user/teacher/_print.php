<?php

use kartik\grid\GridView;
use common\models\Lesson;
?>
<style>
@media print{
	.text-left{
        text-align: left !important;
    }
	.boxed {
	  border: 4px solid #949599;
	  height: 200px;
	  margin: 20px;
	  padding: 20px;
	  width: 580px;
	}
	.sign {
	  font-weight: bold;
	  text-align : right;
	  font-size : 28px;
	}
	.sign span {
	  width: 250px;
	  display: inline-block;
	  border-bottom: 1px solid #999999;
	  font-weight: normal;
	}
}
</style>
<div class="row-fluid col-md-12">
	<div class="logo invoice-col" style="width: 250px">              
		<img class="login-logo-img" src="<?= Yii::$app->request->baseUrl ?>/img/logo.png"  />        
	</div>
	<div class="invoice-col text-gray" style="font-size:18px; width: 200px;">
		<small>
			Arcadia Music Academy ( <?= $model->userLocation->location->name;?> )<br>
			<?php if (!empty($model->userLocation->location->address)): ?>
				<?= $model->userLocation->location->address ?><br>
			<?php endif; ?>
			<?php if (!empty($model->userLocation->location->phone_number)): ?>
				<?= $model->userLocation->location->phone_number ?>
			<?php endif; ?>
			<?php if (!empty($model->userLocation->location->email)): ?>
				<?= $model->userLocation->location->email ?>
			<?php endif; ?> 
		</small> 
	</div>
	<div class="invoice-col" style="width: 220px;">
		To<br>
		<strong>
			<?php echo isset($model->publicIdentity) ? $model->publicIdentity : null ?>
		</strong>
		<address>
			<?php
			$addresses = $model->addresses;
			foreach ($addresses as $address) {
				if ($address->label === 'Billing') {
					$billingAddress = $address;
					break;
				}
			}
			$phoneNumber = $model->phoneNumber;
			?>
			<!-- Billing address -->
			<?php if (!empty($billingAddress)) {
				?>
				<?php
				echo $billingAddress->address . '<br> ' . $billingAddress->city->name . ', ';
				echo $billingAddress->province->name . '<br>' . $billingAddress->country->name . ' ';
				echo $billingAddress->postal_code;
			}
			?>
			<div class="row-fluid m-t-5">
				<?php if (!empty($model->email)): ?>
					<?php echo 'E: '; ?><?php echo $model->email ?>
				<?php endif; ?>
			</div>
            <!-- Phone number -->
            <div class="row-fluid text-gray">
				<?php if (!empty($phoneNumber)) {
					?><?php echo 'P: '; ?>
					<?php echo $phoneNumber->number;
				}
				?>
            </div>
		</address>
	</div>
	<div class="invoice-col"  style="width: 125px;">
		<b>Date:</b> <?= $fromDate->format('l, jS Y') . ' to ' . $toDate->format('l, jS Y'); ?><br>
	</div>
	<div class="clearfix"></div>
</div>
<h4 class="col-md-12"><b>Teacher Time Voucher for <?= $fromDate->format('F jS, Y') . ' to ' . $toDate->format('F jS, Y');?></b></h4>
<?php
if(!$searchModel->summariseReport) {
$columns = [
		[
		'value' => function ($data) {
			$lessonDate = \DateTime::createFromFormat('Y-m-d H:i:s', $data->date);
			$date = $lessonDate->format('l, F jS, Y');
			return !empty($date) ? $date : null;
		},
		'contentOptions' => ['class' => 'text-left'],
		'group' => true,
		'groupedRow' => true,
		'groupFooter' => function ($model, $key, $index, $widget) {
			return [
				'mergeColumns' => [[1, 3]],
				'content' => [
					4 => GridView::F_SUM,
					6 => GridView::F_SUM,
				],
				'contentFormats' => [
					4 => ['format' => 'number', 'decimals' => 2],
					6 => ['format' => 'number', 'decimals' => 2],
				],
				'contentOptions' => [
					4 => ['style' => 'text-align:right'],
					6 => ['style' => 'text-align:right'],
				],
				'options' => ['style' => 'font-weight:bold;']
			];
		}
	],
		[
		'label' => 'Time',
		'width' => '250px',
		'value' => function ($data) {
			return !empty($data->date) ? Yii::$app->formatter->asTime($data->date) : null;
		},
	],
		[
		'label' => 'Program',
		'width' => '250px',
		'value' => function ($data) {
			return !empty($data->enrolment->program->name) ? $data->enrolment->program->name : null;
		},
	],
		[
		'label' => 'Student',
		'value' => function ($data) {
			$student = ' - ';
			if($data->course->program->isPrivate()) {
				$student = !empty($data->enrolment->student->fullName) ? $data->enrolment->student->fullName : null;
			}
			return $student;
		},
	],
		[
		'label' => 'Duration(hrs)',
		'value' => function ($data) {
			return $data->getDuration();
		},
		'contentOptions' => ['class' => 'text-right'],
		'hAlign' => 'right',
		'pageSummary' => true,
		'pageSummaryFunc' => GridView::F_SUM
	],
	[
		'label' => 'Rate/hour',
		'format'=>['decimal',2],
		'value' => function ($data) {
			return $data->teacherRate;
		},
		'hAlign' => 'right',
		'contentOptions' => ['class' => 'text-right'],
	],
	[
		'label' => 'Cost',
		'format' => ['decimal', 2],
		'value' => function ($data) {
			return $data->getDuration() * $data->teacherRate;
		},
		'contentOptions' => ['class' => 'text-right'],
		'hAlign' => 'right',
		'pageSummary' => true,
		'pageSummaryFunc' => GridView::F_SUM
	],
];
} else {
	$columns = [
		[
			'label' => 'Date',
			'value' => function ($data) {
				if( ! empty($data->date)) {
					$lessonDate = \DateTime::createFromFormat('Y-m-d H:i:s', $data->date);
					return $lessonDate->format('l, F jS, Y');
				}

				return null;
			},
		],	
		[
			'label' => 'Duration(hrs)',
			'value' => function ($data){
				$locationId = Yii::$app->session->get('location_id');
				$lessons = Lesson::find()
					->location($locationId)
					->notDeleted()
					->andWhere(['status' => [Lesson::STATUS_COMPLETED, Lesson::STATUS_MISSED, Lesson::STATUS_SCHEDULED]])
					->andWhere(['DATE(date)' => (new \DateTime($data->date))->format('Y-m-d'), 'lesson.teacherId' => $data->teacherId])
					->all();
				$totalDuration = 0;
				foreach($lessons as $lesson) {
					$duration		 = \DateTime::createFromFormat('H:i:s', $lesson->duration);
					$hours			 = $duration->format('H');
					$minutes		 = $duration->format('i');
					$lessonDuration	 = $hours + ($minutes / 60);
					$totalDuration += $lessonDuration;	
				}
				return $totalDuration;
			},
			'contentOptions' => ['class' => 'text-right'],
			'hAlign'=>'right',
			'pageSummary'=>true,
            'pageSummaryFunc'=>GridView::F_SUM
		],
		[
			'label' => 'Cost',
		'format'=>['decimal',2],
		'value' => function ($data) {
				$locationId = Yii::$app->session->get('location_id');
				$lessons = Lesson::find()
					->location($locationId)
					->notDeleted()
					->andWhere(['DATE(date)' => (new \DateTime($data->date))->format('Y-m-d'), 'lesson.teacherId' => $data->teacherId])
					->andWhere(['status' => [Lesson::STATUS_COMPLETED, Lesson::STATUS_MISSED, Lesson::STATUS_SCHEDULED]])
					->all();
				$cost = 0;
				foreach($lessons as $lesson) {
					$duration		 = \DateTime::createFromFormat('H:i:s', $lesson->duration);
					$hours			 = $duration->format('H');
					$minutes		 = $duration->format('i');
					$lessonDuration	 = $hours + ($minutes / 60);
					$cost += $lessonDuration * $data->teacherRate;	
				}
				return $cost;
		},
		'contentOptions' => ['class' => 'text-right'],
			'hAlign'=>'right',
			'pageSummary'=>true,
            'pageSummaryFunc'=>GridView::F_SUM
	],
	];
}
?>
<?=
GridView::widget([
	'dataProvider' => $teacherLessonDataProvider,
	'options' => ['class' => 'col-md-12'],
	'tableOptions' => ['class' => 'table table-responsive table-more-condensed'],
	'headerRowOptions' => ['class' => 'bg-light-gray-1'],
	'pjax' => true,
	'showPageSummary' => true,
	'pjaxSettings' => [
		'neverTimeout' => true,
		'options' => [
			'id' => 'teacher-lesson-grid',
		],
	],
	'columns' => $columns,
]);
?>
<div class="boxed col-md-12 pull-right">
<div class="sign">
 Teacher Signature <span></span>
</div>
<div class="sign">
Authorizing Signature <span></span>
</div>
<div class="sign">
 Date <span></span>
</div>
</div>
<script>
    $(document).ready(function () {
        window.print();
    });
</script>