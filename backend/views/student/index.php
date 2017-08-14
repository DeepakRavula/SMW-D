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
<div class="student-index">  
	
    <div class="smw-search"> 
      <?php
      $form = ActiveForm::begin([
                  'action' => ['index'],
                  'method' => 'get',
                  'options' => ['class' => 'pull-left'],
      ]);
      ?>
      <?php ActiveForm::end(); ?>
      <a id="print" class="btn btn-default m-l-20">
        <i class="fa fa-print"></i> Print all
    </a>
    </div>  
    
    <?php
      $form = ActiveForm::begin([
                  'action' => ['index'],
                  'method' => 'get',
                  'options' => ['class' => 'pull-left'],
      ]);
      ?>
<div class="pull-right  m-r-20">
	<?php yii\widgets\Pjax::begin() ?>
    <div class="schedule-index">
        <div class="e1Div">
        <?= $form->field($searchModel, 'showAllStudents')->checkbox(['data-pjax' => true])->label('Show All'); ?>
        </div>
    </div>
    
    <?php \yii\widgets\Pjax::end(); ?>
	
</div>
<?php ActiveForm::end(); ?>
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
      var url = "<?php echo Url::to(['student/index']); ?>?StudentSearch[showAllStudents]=" + (showAllStudents | 0);
      $.pjax.reload({url:url,container:"#student-listing",replace:false,  timeout: 4000});  //Reload GridView
  });  
  $("#print").on("click", function() {
	  	var showAll = $("#studentsearch-showallstudents").is(":checked");
        var params = $.param({ 'StudentSearch[showAllStudents]': (showAll | 0) });
        var url = '<?php echo Url::to(['student/print']); ?>?' + params;
        window.open(url,'_blank');
    });
});
  </script>