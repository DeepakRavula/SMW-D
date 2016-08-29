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
<style>
  .e1Div{
    right: 0 !important;
    top: -115px;
  }
</style>

<div class="col-md-5">
<h4 class="pull-left m-r-20"><?php echo $titleName; ?></h4>
<div class="pull-left m-l-10 m-r-20">
<?php $form = ActiveForm::begin(['options' => ['data-pjax' => true ]]); ?>
	<div class="schedule-index">
		<div class="e1Div">
			<?= $form->field($searchModel, 'showAllPrograms')->checkbox(['data-pjax' => true])->label('Show All'); ?>
		</div>
	</div>
 <?php ActiveForm::end(); ?>
</div>
<a href="#" class="add-new-program pull-right text-add-new p-l-20"><i class="fa fa-plus-circle m-l-20"></i> Add</a>
<div class="clearfix"></div>
</div>
<div class="clearfix"></div>

<div class="dn program-create section-tab form-well form-well-smw">
    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>

<div class="program-index">
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
      var url = "<?php echo Url::to(['program/index']);?>?ProgramSearch[showAllPrograms]=" + (showAllPrograms | 0) + '&ProgramSearch[type]=' + <?php echo $searchModel->type;?>;
      $.pjax.reload({url:url,container:"#program-listing",replace:false,  timeout: 4000});  //Reload GridView
  });
});
  </script>
