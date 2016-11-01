<?php

use common\models\Program;
use backend\models\search\ProgramSearch;
use yii\bootstrap\Tabs;

$this->title = 'Programs';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="tabbable-panel">
     <div class="tabbable-line">
<?php 

$indexProgram = $this->render('_index-program', [
    'model' => $model,
    'searchModel' => $searchModel,
    'dataProvider' => $dataProvider,
]);

?>

<?php echo Tabs::widget([
    'items' => [
        [
            'label' => 'Private Programs',
            'content' => (int) $searchModel->type === Program::TYPE_PRIVATE_PROGRAM ? $indexProgram : null,
            'url' => ['/program/index', 'ProgramSearch[type]' => Program::TYPE_PRIVATE_PROGRAM],
            'active' => (int) $searchModel->type === Program::TYPE_PRIVATE_PROGRAM,
        ],
        [
            'label' => 'Group Programs',
            'content' => (int) $searchModel->type === Program::TYPE_GROUP_PROGRAM ? $indexProgram : null,
            'url' => ['/program/index', 'ProgramSearch[type]' => Program::TYPE_GROUP_PROGRAM],
            'active' => (int) $searchModel->type === Program::TYPE_GROUP_PROGRAM,
        ],
    ],
]); ?>
<div class="clearfix"></div>
</div>
</div>
<script>
$(document).ready(function() {
	$('.add-new-program').click(function(){
		$('.program-create').show();
  });
});
</script>