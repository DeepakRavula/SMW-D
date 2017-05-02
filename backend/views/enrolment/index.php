<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\EnrolmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Enrolments';
$this->params['action-button'] = Html::a(Yii::t('backend', '<i class="fa fa-plus-circle" aria-hidden="true"></i> Add'), ['create'], ['class' => 'btn btn-primary btn-sm']);
?>
<style>
	.e1Div{
		top: -51px;
		right: 76px;
	}
</style>
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
<div class="enrolment-index">
    <?php yii\widgets\Pjax::begin(['id' => 'enrolment-index']); ?>
	<?php
	echo GridView::widget([
		'dataProvider' => $dataProvider,
		'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
		'columns' => [
			[
				'label' => 'Program',
				'value' => function($data) {
					return $data->course->program->name;
				}
			],
			[
				'label' => 'Student',
				'value' => function($data) {
					return $data->student->fullName;
				}
			],
			[
				'label' => 'Teacher',
				'value' => function($data) {
					return $data->course->teacher->publicIdentity;
				}
			],
			[
				'label' => 'Expiry Date',
				'value' => function($data) {
					return Yii::$app->formatter->asDate($data->course->endDate);
				}
			],
		],
	]);
	?>
<?php yii\widgets\Pjax::end(); ?>
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