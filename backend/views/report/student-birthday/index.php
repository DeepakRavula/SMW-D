<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Student Birthdays';

?>
<div class="payments-index p-10">
    
	<?php echo $this->render('_search', ['model' => $searchModel]); ?>
  
</div>

<div class="clearfix"></div>
<div class="grid-row-open"> 
    <?php yii\widgets\Pjax::begin(['id' => 'birthday-listing']); ?>
    <?php
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'rowOptions' => function ($model, $key, $index, $grid) {
            $url = Url::to(['student/view', 'id' => $model->id]);
            $data = ['data-url' => $url];
            return $data;
        },
        'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'columns' => [
            'first_name',
            'last_name',
			'birth_date:date',
			[
				'label' => 'Customer',
				'value' => 'customer.userProfile.fullName', 
			],
			[
				'label' => 'Phone',
				'value' => 'customer.phoneNumber.number', 
			],
			'customer.email',
        ],
    ]);

    ?>

<?php yii\widgets\Pjax::end(); ?>
</div>
<script>
$(document).ready(function(){
        $("#print").on("click", function() {           
        var dateRange = document.getElementById('studentbirthdaysearch-daterange').value;
        var params = $.param({ 'StudentBirthdaySearch[dateRange]': dateRange});
        var url = '<?php echo Url::to(['student-birthday/print']); ?>?' + params;
        window.open(url,'_blank');
    });
});
</script>