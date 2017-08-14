<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\bootstrap\ActiveForm;
use backend\models\search\ProgramSearch;
use common\models\Program;
use common\models\User;
use common\components\gridView\AdminLteGridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
$lastRole = end($roles);
$this->params['action-button'] = Html::a('<i class="fa fa-plus-circle"></i> Add', ['create'], ['class' => 'btn btn-primary btn-sm', 'id' => 'add-program']);
?>
<div>
<div>
    <?php
    $form = ActiveForm::begin([
                'action' => ['index'],
                'method' => 'get',
                'options' => ['class' => 'pull-left'],
    ]);
    ?>
</div>
<div>
			<?= $form->field($searchModel, 'showAllPrograms')->checkbox(['data-pjax' => true])->label('Show All'); ?>
</div>
   	<?php echo $form->field($searchModel, 'type')->hiddenInput()->label(false); ?>
    <?php ActiveForm::end(); ?>	
    <div>
	<?php $rateLabel = (int) $model->type === Program::TYPE_PRIVATE_PROGRAM ? 'Rate Per Hour' : 'Rate Per Course'; ?>
	<?php Pjax::begin(['id' => 'program-listing']) ?>
        <?php echo AdminLteGridView::widget([
			'id' => 'program-grid',
            'dataProvider' => $dataProvider,
    		'filterModel' => $searchModel,
			'condensed' => true,
        	'hover' => true,
            'columns' => [
                'name',
                [
                'label' => $rateLabel,
				'attribute' => 'rate', 
                'value' => function ($data) {
                    return !empty($data->rate) ? Yii::$app->formatter->asCurrency($data->rate) : null;
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
      var url = "<?php echo Url::to(['program/index']); ?>?ProgramSearch[query]=" + "<?= $searchModel->query; ?>&ProgramSearch[showAllPrograms]=" + (showAllPrograms | 0) + '&ProgramSearch[type]=' + "<?php echo $searchModel->type; ?>";
      $.pjax.reload({url:url,container:"#program-listing",replace:false,  timeout: 4000});  //Reload GridView
  });
});
  </script>
