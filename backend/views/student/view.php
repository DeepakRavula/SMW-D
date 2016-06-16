<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use common\models\Enrolment;
use common\models\User;
use common\models\Lesson;
use common\models\Invoice;
use common\models\Student;
use yii\bootstrap\Tabs;

/* @var $this yii\web\View */
/* @var $model common\models\Student */
$this->title = 'Student Details';
$this->params['breadcrumbs'][] = ['label' => 'Students', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php 



$profileContent = $this->render('_profile',[
		'model'	=> $model,
]);

$enrolmentContent =  $this->render('_enrolment', [
	'dataProvider' => $dataProvider,
    'enrolmentModel' => $enrolmentModel,
]);

$lessonContent =  $this->render('_lesson', [
	'lessonModel' => $lessonModel,
]);
?>
<?php echo Tabs::widget([
    'items' => [
        [
            'label' => 'Profile',
            'content' => $profileContent,
            'active' => true
        ],
        [
            'label' => 'Enrolments',
            'content' => $enrolmentContent,
        ],
		[
            'label' => 'Lessons',
            'content' => $lessonContent,
        ],
    ],
]);?>

<script>
	$('.add-new-program').click(function(){
		$('.enrolment-create').show(); 
	});
</script>


