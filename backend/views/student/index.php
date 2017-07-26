<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use common\models\Student;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Students';
$this->params['breadcrumbs'][] = $this->title;
?> 
<?php $this->registerCssFile("@web/css/student/style.css");?>
<style>
  .e1Div{
    right: 0 !important;
    top: -59px;
  }
</style>
<div class="student-index">  
	<div id="print" class="btn btn-default">
        <?= Html::a('<i class="fa fa-print"></i> Print') ?>
    </div>
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
	<?php yii\widgets\Pjax::begin() ?>
    <div class="schedule-index">
        <div class="e1Div">
        <?= $form->field($searchModel, 'showAllStudents')->checkbox(['data-pjax' => true])->label('Show All'); ?>
        </div>
    </div>
    
    <?php \yii\widgets\Pjax::end(); ?>
	<?php ActiveForm::end(); ?>
</div>
<div class="grid-row-open"> 
<?= $this->render('_index', [
	'dataProvider' => $dataProvider,
	'searchModel' => $searchModel
]);?>
    </div>
</div>
<script>
$(document).ready(function(){
  $("#studentsearch-showallstudents").on("change", function() {
      var showAllStudents = $(this).is(":checked");
      var url = "<?php echo Url::to(['student/index']); ?>?StudentSearch[query]=" + "<?php echo $searchModel->query; ?>&StudentSearch[showAllStudents]=" + (showAllStudents | 0);
      $.pjax.reload({url:url,container:"#student-listing",replace:false,  timeout: 4000});  //Reload GridView
  });  
  $("#print").on("click", function() {
        var url = '<?php echo Url::to(['student/print']); ?>';
        window.open(url,'_blank');
    });
});
  </script>