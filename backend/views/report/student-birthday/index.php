<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use common\components\gridView\AdminLteGridView;
use common\components\gridView\KartikGridView;
use kartik\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<div class="form-group form-inline">
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
</div>
<div class="clearfix"></div>
<div class="grid-row-open"> 
<?php Pjax::begin(['id' => 'birthday-listing']); ?>
    <?= KartikGridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => false,
        'emptyText' => false,
        'rowOptions' => function ($model, $key, $index, $grid) {
            $url = Url::to(['student/view', 'id' => $model->id]);
            $data = ['data-url' => $url];
            return $data;
        },
        'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'columns' => [
            [
                'label' => 'Name',
                'value' => function ($data) {
                    return $data->fullName;
                },
            ],
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
        'toolbar' => [
            ['content' => Html::a('<i class="fa fa-print btn-default btn-lg"></i>', '#', ['id' => 'print'])],
        ],
        'panel' => [
            'type' => GridView::TYPE_DEFAULT,
            'heading' => 'Student Birthdays'
        ],
    ]);

    ?>

<?php Pjax::end(); ?>
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
