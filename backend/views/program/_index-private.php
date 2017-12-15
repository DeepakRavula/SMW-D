<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\bootstrap\ActiveForm;
use common\models\Program;
use yii\grid\GridView;
use yii\helpers\Html;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
$lastRole = end($roles);
$this->params['action-button'] = Html::a('<i class="fa fa-plus"></i> Add', ['create'], ['class' => 'btn btn-primary btn-sm', 'id' => 'add-program']);
$this->params['show-all'] = $this->render('_button', [
	'searchModel' => $searchModel
]);
?>
<div>
    <?php
		LteBox::begin([
			'type' => LteConst::TYPE_DEFAULT,
			'boxTools' => [
				'<i title="Edit" class="fa fa-pencil student-profile-edit-button m-r-10"></i>',
				'<i title="Merge" id="student-merge" class="fa fa-chain"></i>'
			],
			'title' => 'Private Programs',
			'withBorder' => true,
		])
		?>
	<?php $rateLabel = (int) $model->type === Program::TYPE_PRIVATE_PROGRAM ? 'Rate Per Hour' : 'Rate Per Course'; ?>
	<?php Pjax::begin(['id' => 'private-program-listing']) ?>
        <?php echo GridView::widget([
			'id' => 'private-program-grid',
            'dataProvider' => $privateDataProvider,
    		'filterModel' => $searchModel,
			//'condensed' => true,
        	//'hover' => true,
            'columns' => [
                [
					'attribute' => 'name', 
					'contentOptions' => ['style' => 'width:250px;'],
					'value' => function ($data) {
						return $data->name;
					},
				],
                [
					'label' => 'Rate Per Hour',
					'attribute' => 'rate', 
					'format' => 'currency',
					'headerOptions' => ['class' => 'text-right'],
					'contentOptions' => ['class' => 'text-right', 'style' => 'width:100px;'],
					'value' => function ($data) {
						return !empty($data->rate) ? $data->rate : null;
					},
				],
            ],
        ]); ?>
    <?php Pjax::end(); ?>
    <?php LteBox::end() ?>
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
