<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use common\models\Student;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->registerCssFile("@web/css/student/style.css");
$this->title = 'Students';
$this->params['action-button'] = Html::a('<i class="fa fa-print"> Print</i>', '#', ['class' => "btn bg-primary btn-sm", 'id' => 'print']);
$this->params['show-all'] = $this->render('_button', [
	'searchModel' => $searchModel
]);
?> 

<div class="grid-row-open"> 
<?= $this->render('_index', [
	'dataProvider' => $dataProvider,
	'searchModel' => $searchModel
]);?>
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