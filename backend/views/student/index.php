<?php

use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Students';
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
  .e1Div{
    right: 0 !important;
  }
</style>
<div class="student-index">     
    <i class="fa fa-search m-l-20 m-t-5 pull-left m-r-10 f-s-16"></i>
    <?php
    $form = ActiveForm::begin([
                'action' => ['index'],
                'method' => 'get',
                'options' => ['class' => 'pull-left'],
    ]);
    ?>
    <?=
    $form->field($searchModel, 'query', [
        'inputOptions' => [
            'placeholder' => 'Search ...',
            'class' => 'search-field',
        ],
    ])->input('search')->label(false);
    ?>
    <?php $queryParams = Yii::$app->request->queryParams; ?> 
    <?php foreach ($queryParams as $queryParam => $queryValues): ?> 
        <?php if(is_array($queryValues)) : ?>
            <?php foreach ($queryValues as $param => $value): ?> 
                <?php if ($param === 'query') continue; ?>
                <?= Html::input('hidden', $queryParam . '[' . $param . ']', $value, ['class' => 'form-control']) ?>
            <?php endforeach; ?>
        <?php endif; ?> 
    <?php endforeach; ?>
<?php ActiveForm::end(); ?>
    
<div class="pull-right  m-r-20">
	<?php yii\widgets\Pjax::begin() ?>
	<?php $form = ActiveForm::begin(['options' => ['data-pjax' => true ]]); ?>
    <?= $form->field($searchModel, 'showAllStudents')->checkbox(['data-pjax' => true, 'class'=>'adsf']); ?>
	<?php ActiveForm::end(); ?>
    <?php \yii\widgets\Pjax::end(); ?>
</div>
<?php yii\widgets\Pjax::begin(['id' => 'student-listing']); ?>
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'rowOptions' => function ($model, $key, $index, $grid) {
            $u= \yii\helpers\StringHelper::basename(get_class($model));
            $u= yii\helpers\Url::toRoute(['/'.strtolower($u).'/view']);
            return ['id' => $model['id'], 'style' => "cursor: pointer", 'onclick' => 'location.href="'.$u.'?id="+(this.id);'];
        },
        'tableOptions' =>['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray' ],
        'columns' => [
			[
                'attribute' => 'first_name',
				'label' => 'First Name',
				'value' => function($data) {
					return ! (empty($data->first_name)) ? $data->first_name : null;
                } 
			],
			'last_name',
			[
                'attribute' => 'customer_id',
				'label' => 'Customer Name',
				'value' => function($data) {
					$fullName = ! (empty($data->customer->userProfile->fullName)) ? $data->customer->userProfile->fullName : null;
					return $fullName;
                } 
			],
        ],
    ]); ?>

	<?php yii\widgets\Pjax::end(); ?>
</div>
<script>
$(document).ready(function(){
  $("#studentsearch-showallstudents").on("change", function() {
      var showAllStudents = $(this).is(":checked");
      var url = "<?php echo Url::to(['student/index']);?>?StudentSearch[query]=" + "<?php echo $searchModel->query;?>&StudentSearch[showAllStudents]=" + (showAllStudents | 0);
      $.pjax.reload({url:url,container:"#student-listing",replace:false,  timeout: 4000});  //Reload GridView
  });
});
  </script>