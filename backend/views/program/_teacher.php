<?php 

use yii\grid\GridView;
use yii\helpers\Url;

?>
<div class="grid-row-open">
<?php yii\widgets\Pjax::begin([
    'timeout' => 6000,
]) ?>

<?php
echo GridView::widget([
'dataProvider' => $teacherDataProvider,
'tableOptions' => ['class' => 'table table-bordered'],
'headerRowOptions' => ['class' => 'bg-light-gray'],
'summary' => false,
'emptyText' => false,
'rowOptions' => function ($model, $key, $index, $grid) {
    $url = Url::to(['user/view', 'UserSearch[role_name]' => 'teacher', 'id' => $model->id]);

    return ['data-url' => $url];
},
'options' => ['class' => 'col-md-4'],
'columns' => [
    [
        'label' => 'Teacher Name',
        'value' => function ($data) {
            return !empty($data->publicIdentity) ? $data->publicIdentity : null;
        },
    ],
],
]);
?>
<?php \yii\widgets\Pjax::end(); ?>
<div class="clearfix"></div>
</div>
