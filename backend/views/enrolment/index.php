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

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\EnrolmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = 'Enrolments';
$this->params['action-button'] = Html::a(Yii::t('backend', '<i class="fa fa-plus f-s-18" aria-hidden="true"></i>'), ['create']);
$this->params['show-all'] = $this->render('_button', [
	'searchModel' => $searchModel
]);
?>
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
				'format'=>'raw'
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
						'allowClear'=>false,
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
<script>
$(document).ready(function(){
  $("#enrolmentsearch-showallenrolments").on("change", function() {
      var showAllEnrolments = $(this).is(":checked");
      var url = "<?php echo Url::to(['enrolment/index']); ?>?EnrolmentSearch[showAllEnrolments]=" + (showAllEnrolments | 0);
      $.pjax.reload({url:url,container:"#enrolment-listing",replace:false,  timeout: 4000});  //Reload GridView
    });
});
</script>