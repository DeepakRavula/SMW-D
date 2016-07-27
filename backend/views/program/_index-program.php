<?php

use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\bootstrap\ActiveForm;
use backend\models\search\ProgramSearch;
use common\models\Program;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$titleName = (int) $searchModel->type === ProgramSearch::TYPE_PRIVATE_PROGRAM ? 'Private Programs' : 'Group Program'; 

?>
<div class="col-md-12">
<h4 class="pull-left m-r-20"><?php echo $titleName; ?></h4>
<a href="#" class="add-new-program text-add-new"><i class="fa fa-plus"></i></a>
<div class="clearfix"></div>
</div>

<div class="dn program-create section-tab">
    <?php echo $this->render('_form', [
        'model' => $searchModel,
    ]) ?>
</div>

<div class="program-index m-t-20">
<div class="pull-right  m-r-20">
	<?php yii\widgets\Pjax::begin() ?>
	<?php $form = ActiveForm::begin(['options' => ['data-pjax' => true ]]); ?>
	<?= $form->field($searchModel, 'activeOnly')->checkbox(['data-pjax' => true]); ?>
	<?php ActiveForm::end(); ?>
    <?php \yii\widgets\Pjax::end(); ?>
</div>
	<?php Pjax::begin(['id' => 'program-listing']) ?>
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
    <?php Pjax::end(); ?>
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
