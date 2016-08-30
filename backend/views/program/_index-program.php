<?php

use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\bootstrap\ActiveForm;
use backend\models\search\ProgramSearch;
use common\models\Program;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$titleName = (int) $searchModel->type === ProgramSearch::TYPE_PRIVATE_PROGRAM ? 'Private Programs' : 'Group Programs'; 

?>
<style>
  .e1Div{
		top: -61px;
  }
</style>

<div class="program-index">
<div class="smw-search">
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
</div>
<div class="pull-right  m-r-20">
	<div class="schedule-index">
		<div class="e1Div">
			<?= $form->field($searchModel, 'showAllPrograms')->checkbox(['data-pjax' => true])->label('Show All'); ?>
		</div>
    </div>
</div>
    	<?php echo $form->field($searchModel, 'type')->hiddenInput()->label(false); ?>
    <?php ActiveForm::end(); ?>
	
<div class="col-md-5">
<h4 class="pull-left m-r-20"><?php echo $titleName; ?></h4>
<a href="#" class="add-new-program pull-right text-add-new p-l-20"><i class="fa fa-plus-circle m-l-20"></i> Add</a>
<div class="clearfix"></div>
</div>
<div class="clearfix"></div>

<div class="dn program-create section-tab form-well form-well-smw">
    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>
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
  $("#programsearch-showallprograms").on("change", function() {
      var showAllPrograms = $(this).is(":checked");
      var url = "<?php echo Url::to(['program/index']);?>?ProgramSearch[query]=" + "<?= $searchModel->query;?>&ProgramSearch[showAllPrograms]=" + (showAllPrograms | 0) + '&ProgramSearch[type]=' + "<?php echo $searchModel->type;?>";
      $.pjax.reload({url:url,container:"#program-listing",replace:false,  timeout: 4000});  //Reload GridView
  });
});
  </script>
