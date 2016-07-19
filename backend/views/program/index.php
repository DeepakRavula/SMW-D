<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Programs';
$this->params['subtitle'] = Html::a('<i class="fa fa-plus" aria-hidden="true"></i>', ['create'], ['class' => 'btn btn-success']);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="program-index m-t-20">
<div class="pull-right  m-r-20">
	<?php yii\widgets\Pjax::begin(['enablePushState' => false]) ?>
	<?php $form = ActiveForm::begin(['options' => ['data-pjax' => true ]]); ?>
	<?= $form->field($searchModel, 'activeOnly')->checkbox(['data-pjax' => true]); ?>
	<?php ActiveForm::end(); ?>
    <?php \yii\widgets\Pjax::end(); ?>
</div>
	<?php yii\widgets\Pjax::begin(['id' => 'program-listing']) ?>
        <?php echo GridView::widget([
            'dataProvider' => $dataProvider,
            'options' => ['class'=>'col-md-5'],
            'tableOptions' =>['class' => 'table table-bordered'],
            'headerRowOptions' => ['class' => 'bg-light-gray' ],
            'rowOptions' => function ($model, $key, $index, $grid) {
                $u= \yii\helpers\StringHelper::basename(get_class($model));
                $u= yii\helpers\Url::toRoute(['/'.strtolower($u).'/view']);
                return ['id' => $model['id'], 'style' => "cursor: pointer", 'onclick' => 'location.href="'.$u.'?id="+(this.id);'];
            },
            'columns' => [
                'name',
                'rate:currency',
            ],
        ]); ?>
    <?php \yii\widgets\Pjax::end(); ?>
    <div class="clearfix"></div>
</div>
  <script>
$(document).ready(function(){
  $("#programsearch-activeonly").on("change", function() {
      var activeOnly = $(this).is(":checked");
      var url = "<?php echo Url::to(['program/index']);?>?ProgramSearch[activeOnly]=" + (activeOnly | 0);
      $.pjax.reload({url:url,container:"#program-listing",replace:false,  timeout: 4000});  //Reload GridView
  });
});
  </script>
