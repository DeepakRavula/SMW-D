<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\Lesson;
use common\models\Invoice;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lessons';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lesson-index">

<?php yii\widgets\Pjax::begin(['id' => 'lesson-index']); ?>
    <?php echo GridView::widget([
        'dataProvider' => $unInvoicedLessonsDataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
			'id'
        ],
    ]); ?>
	<?php yii\widgets\Pjax::end(); ?>

</div>

<?php
$this->registerJs(
   '$("document").ready(function(){ 
        $("#new_medicine").on("pjax:end", function() {
            $.pjax.reload({container:"#medicine"});  //Reload GridView
        });
    });'
);
?>
