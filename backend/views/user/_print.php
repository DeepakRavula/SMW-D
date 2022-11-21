<?php

use common\models\User;
use common\models\Invoice;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
use common\components\gridView\KartikGridView;
use kartik\grid\GridView;
use common\models\Location;

?>
<?php $model = Location::findOne(['id' => \common\models\Location::findOne(['slug' => \Yii::$app->location])->id]);
?>
<h2 class="col-md-12"><b><?= ucfirst($searchModel->role_name) . ' list for '. $model->name ;?></b></h2>
<div class="user-index"> 
    <?php Pjax::begin([
        'id' => 'user-index',
        'timeout' => 6000
    ]); ?>
    <?php 
    $roleName = $searchModel->role_name;
    ?>
<div class="grid-row-open">
<?php
            $columns = [
            [
                'label' => 'First Name',
                'value' => function ($data) {
                    return !empty($data->userProfile->firstname) ? $data->userProfile->firstname : null;
                },
            ],
            [
                'label' => 'Last Name',
                'value' => function ($data) {
                    return !empty($data->userProfile->lastname) ? $data->userProfile->lastname : null;
                },
            ],

            ];
            if ($roleName == User::ROLE_TEACHER) {
                array_push($columns,[
                'label' => 'Email',
                'value' => function ($data) {
                    return !empty($data->getEmail()) ? $data->getEmail() : null;
                },
            ],
            [
                'label' => 'Phone',
                'value' => function ($data) {
                    return !empty($data->getPhone()) ? $data->getPhone() : null;
                },
            ]);
            }    
            if ($roleName == User::ROLE_CUSTOMER) {
                array_push($columns,[
                'label' => 'Student',
                'value' => function ($data) {
                    return !empty($data->student) ? $data->getStudentsList() : null;
                },
            ],
            [
                'label' => 'Balance',
                    'value' => function ($data) {
                        return round($data->customerAccount->balance, 2);
                },
                'contentOptions' => ['class' => 'text-right dollar', 'style' => 'width:20%'],
                    'hAlign' => 'right',
                    'pageSummary' => true,
                    'pageSummaryFunc' => GridView::F_SUM
            ]);
       } ?>
        <?= KartikGridView::widget([
            'dataProvider' => $dataProvider,
            'summary' => false,
            'emptyText' => false,
            'tableOptions' => ['class' => 'table table-bordered'],
            'headerRowOptions' => ['class' => 'bg-light-gray'],
            'showPageSummary' => true,
            'columns' => $columns,
        ]);?>
        
</div>
<?php Pjax::end(); ?>

<script>
    $(document).ready(function () {
        window.print();
    });
</script>