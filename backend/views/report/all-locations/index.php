<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use common\components\gridView\AdminLteGridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'All Locations';
?>
<div class="form-group form-inline">
<?php echo $this->render('_search', ['model' => $searchModel]); ?>
</div>
<div class="clearfix"></div>
<div class="grid-row-open"> 
    <?php Pjax::begin(['id' => 'locations-listing']); ?>
    <?php
    echo AdminLteGridView::widget([
        'dataProvider' => $dataProvider,
        'rowOptions' => function ($model, $key, $index, $grid) {
            $url = Url::to(['student/view', 'id' => $model->id]);
            $data = ['data-url' => $url];
            return $data;
        },
        'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'columns' => [
            'name',
                [
                'label' => 'Active Students',
                'value' => function ($data) use ($searchModel) {
                    return !empty($data->getActiveStudentsCount($searchModel->fromDate, $searchModel->toDate)) ? $data->getActiveStudentsCount($searchModel->fromDate, $searchModel->toDate) : null;
                },
            ],
        ],
    ]);

    ?>
<?php Pjax::end(); ?>
</div>
