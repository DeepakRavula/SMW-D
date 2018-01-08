<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\bootstrap\ButtonGroup;
use yii\bootstrap\ActiveForm;
use common\models\Program;
use yii\grid\GridView;
use yii\helpers\Html;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$roles    = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
$lastRole = end($roles);
?>
 <div class="box">
           <div class="box-body">
<?= ButtonGroup::widget([
	'buttons' => [
		Html::a('Private', '', ['class' => ['btn btn-default active', 'private'],
			'value' => 1]),
		Html::a('Group', '', ['class' => ['btn btn-default', 'group'],
			'value' => 2]),
	]
]); ?>
<div>
   
    <?php Pjax::begin(['id' => 'program-listing', 'enablePushState' => false]) ?>
    <?php
    echo GridView::widget([
        'id' => 'private-program-grid',
        'dataProvider' => $dataProvider,
        
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
    ]);
    ?>
<?php Pjax::end(); ?>
</div>
</div>
    <div class="clearfix"></div>
</div>
<script>
     $(document).ready(function() {
	$(document).on('click', '.private', function() {
		$(".group").removeClass('active');		
		$(".private").addClass('active');	
	});
	$(document).on('click', '.group', function() {
		$(".private").removeClass('active');	
		$(".group").addClass('active');	
	});
	$(document).on('click', '.group, .private', function() {
		var type = $(this).attr('value');
		var url = "<?php echo Url::to(['/program/index']); ?>?ProgramSearch[type]=" + type;
		$.pjax.reload({url:url,container:"#program-listing",replace:false,  timeout: 4000});  
		return false;
	});
});
    $(document).ready(function () {
        $(".show-all-private-programs").on("click", function () {
            var showAllPrograms = $(this).is(":checked");
            var url = "<?php echo Url::to(['program/index']); ?>?ProgramSearch[showAllPrograms]=" + (showAllPrograms | 0) + '&ProgramSearch[programType]=' + "<?php echo Program::TYPE_PRIVATE_PROGRAM; ?>";
            $.pjax.reload({url: url, container: "#private-program-listing", replace: false, timeout: 4000});  //Reload GridView
        });
    });
</script>
