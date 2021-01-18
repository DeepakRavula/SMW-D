<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use common\models\Student;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->registerCssFile("@web/css/student/style.css");
//$this->title = 'Students';
//$this->params['action-button'] = Html::a('<i class="fa fa-print f-s-18 m-l-10"></i>', '#', ['id' => 'print']);
/*$this->params['show-all'] = $this->render('_button', [
    'searchModel' => $searchModel
]);*/
?> 

<div class="grid-row-open"> 
<?= $this->render('_index', [
    'dataProvider' => $dataProvider,
    'searchModel' => $searchModel
]);?>
    </div>
<script>
    $(document).off('change', "#studentsearch-showallstudents").on('change', "#studentsearch-showallstudents", function(){
        var showAllStudents = $(this).is(":checked");
        var firstname_search = $("input[name*='StudentSearch[first_name]").val();
        var lastname_search  = $("input[name*='StudentSearch[last_name]").val();
        var customer_search  = $("input[name*='StudentSearch[customer]").val();
        var phone_search     = $("input[name*='StudentSearch[phone]").val();
        var params = $.param({ 'StudentSearch[showAllStudents]': (showAllStudents | 0),'StudentSearch[first_name]':firstname_search,'StudentSearch[last_name]':lastname_search,'StudentSearch[customer]':customer_search,'StudentSearch[phone]':phone_search });
        var url = "<?php echo Url::to(['student/index']); ?>?"+params;
        $.pjax.reload({url: url, container: "#student-listing", replace: false, timeout: 4000});  //Reload GridView
    });
    $("#print").on("click", function() {
	  	var showAll = $("#studentsearch-showallstudents").is(":checked");
        var params = $.param({ 'StudentSearch[showAllStudents]': (showAll | 0) });
        var url = '<?php echo Url::to(['student/print']); ?>?' + params;
        window.open(url,'_blank');
    });
</script>