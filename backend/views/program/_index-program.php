<?php

use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\bootstrap\ActiveForm;
use backend\models\search\ProgramSearch;
use common\models\Program;
use common\models\User;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$titleName = (int) $searchModel->type === ProgramSearch::TYPE_PRIVATE_PROGRAM ? 'Private Programs' : 'Group Programs'; 
$roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
foreach($roles as $name => $description){
	$role = $name;
}
?>
<style>
  .e1Div{
		top: -61px;
        right: 0;
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
	
<?php if($role === User::ROLE_ADMINISTRATOR):?>
<div class="col-md-5">
<h4 class="pull-left m-r-20"><?php echo $titleName; ?></h4>
<a href="#" class="add-new-program pull-right text-add-new p-l-20"><i class="fa fa-plus-circle m-l-20"></i> Add</a>
<div class="clearfix"></div>
</div>
<?php endif;?>
<div class="clearfix"></div>

<div class="dn program-create section-tab form-well form-well-smw">
    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
    <div class="grid-row-open">
	<?php $rateLabel = (int)$model->type === Program::TYPE_PRIVATE_PROGRAM ? 'Rate Per Hour' : 'Rate Per Course';?>
	<?php Pjax::begin(['id' => 'program-listing']) ?>
        <?php echo GridView::widget([
            'dataProvider' => $dataProvider,
            'options' => ['class'=>'col-md-5'],
            'tableOptions' =>['class' => 'table table-bordered'],
            'headerRowOptions' => ['class' => 'bg-light-gray' ],
            'rowOptions' => function ($model, $key, $index, $grid) {
               $url = Url::to(['program/view', 'id' => $model->id]);
            return ['data-url' => $url];
            },
            'columns' => [
                'name',
                [
				'label' => $rateLabel,
				'value' => function($data) {
					return ! empty($data->rate) ? Yii::$app->formatter->asCurrency($data->rate) : null;
                },
			],
            ],
        ]); ?>
    <?php Pjax::end(); ?>
    </div>
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
