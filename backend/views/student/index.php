<?php

use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Students';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="student-index">
<div class="pull-right  m-r-20">
	<?php yii\widgets\Pjax::begin() ?>
	<?php $form = ActiveForm::begin(['options' => ['data-pjax' => true ]]); ?>
	<?= $form->field($searchModel, 'enrolledStudent')->checkbox(['data-pjax' => true]); ?>
	<?php ActiveForm::end(); ?>
    <?php \yii\widgets\Pjax::end(); ?>
</div>
<?php yii\widgets\Pjax::begin(['id' => 'student-listing']); ?>
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions' => function ($model, $key, $index, $grid) {
            $u= \yii\helpers\StringHelper::basename(get_class($model));
            $u= yii\helpers\Url::toRoute(['/'.strtolower($u).'/view']);
            return ['id' => $model['id'], 'style' => "cursor: pointer", 'onclick' => 'location.href="'.$u.'?id="+(this.id);'];
        },
        'tableOptions' =>['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray' ],
        'columns' => [
			'first_name',
            'last_name',
			[
                'attribute' => 'customer_id',
				'label' => 'Customer Name',
				'value' => function($data) {
					$fullName = ! (empty($data->customer->userProfile->fullName)) ? $data->customer->userProfile->fullName : null;
					return $fullName;
                } 
			],
			[
				'label' => 'Enrolments',
				'value' => function($data) {
					return $data->enrolmentsCount;
                } 
			],
        ],
    ]); ?>

	<?php yii\widgets\Pjax::end(); ?>
</div>
<script>
$(document).ready(function(){
  $("#studentsearch-enrolledstudent").on("change", function() {
      var enrolledStudent = $(this).is(":checked");
      var url = "<?php echo Url::to(['student/index']);?>?StudentSearch[enrolledStudent]=" + (enrolledStudent | 0);
      $.pjax.reload({url:url,container:"#student-listing",replace:false,  timeout: 4000});  //Reload GridView
  });
});
  </script>