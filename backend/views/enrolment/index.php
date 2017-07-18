<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use common\models\Enrolment;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use common\models\Program;
use common\models\Student;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\EnrolmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Enrolments';
$this->params['action-button'] = Html::a(Yii::t('backend', '<i class="fa fa-plus-circle" aria-hidden="true"></i> Add'), ['create'], ['class' => 'btn btn-success btn-sm']);
?>
<style>
	.e1Div{
		top: -51px;
		right: 76px;
	}
</style>
<div class="enrolment-index">
<?php
$form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
		'options' => ['class' => 'pull-left'],
	]);
?>
<div class="pull-right  m-r-20">
	<div class="schedule-index">
		<div class="e1Div">
			<?= $form->field($searchModel, 'showAllEnrolments')->checkbox(['data-pjax' => true]); ?>
		</div>
	</div>
</div>
<?php ActiveForm::end(); ?>
	<?php $columns = [
		[
            'attribute' => 'program',
                'label' => 'Program',
				'value' => function($data) {
					return $data->course->program->name;
				},
				'filterType'=>GridView::FILTER_SELECT2,
				'filter'=>ArrayHelper::map(Program::find()->active()->asArray()->all(), 'id', 'name'), 
				'filterWidgetOptions'=>[
					'pluginOptions'=>['allowClear'=>true],
				],
				'filterInputOptions'=>['placeholder'=>'Any Program'],
				'format'=>'raw'
			],
			[
            'attribute' => 'student',
                'label' => 'Student',
				'value' => function($data) {
					return $data->student->fullName;
				},
			],
			[
            'attribute' => 'teacher',
                'label' => 'Teacher',
				'value' => function($data) {
					return $data->course->teacher->publicIdentity;
				}
			],
			[
            'attribute' => 'expirydate',
                'label' => 'Expiry Date',
				'format' => 'date',
				'value' => function($data) {
					return Yii::$app->formatter->asDate($data->course->endDate);
				},
				'filterType'=>GridView::FILTER_DATE,
			],	
	]; ?>
<div class="grid-row-open">
	<?php
	echo GridView::widget([
		'dataProvider' => $dataProvider,
        'filterModel'=>$searchModel,
		'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'rowOptions' => function ($model, $key, $index, $grid) use ($searchModel) {
        $url = Url::to(['enrolment/view', 'id' => $model->id]);
        $data = ['data-url' => $url];
        if ($model->isExpiring(Enrolment::ENROLMENT_EXPIRY)) {
            $data = array_merge($data, ['class' => 'danger inactive']);
        }
        return $data;
    },
    'columns' => $columns,
	'pjax'=>true,
	'pjaxSettings'=>[
        'id' => 'enrolment-index',
    ]
	]);
	?>
</div>
<script>
$(document).ready(function(){
  $("#enrolmentsearch-showallenrolments").on("change", function() {
      var showAllEnrolments = $(this).is(":checked");
      var url = "<?php echo Url::to(['enrolment/index']); ?>?EnrolmentSearch[showAllEnrolments]=" + (showAllEnrolments | 0);
      $.pjax.reload({url:url,container:"#enrolment-index",replace:false,  timeout: 4000});  //Reload GridView
    });
});
</script>