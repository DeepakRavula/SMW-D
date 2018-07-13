<?php

use common\models\User;
use common\models\Invoice;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use common\models\Location;

?>
<?php $model = Location::findOne(['id' => \common\models\Location::findOne(['slug' => \Yii::$app->location])->id]);
echo " Customers detailes :";
?>

<div class="user-index"> 
    <?php Pjax::begin([
        'id' => 'user-index',
        'timeout' => 6000
    ]); ?>
<div class="grid-row-open">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'summary' => false,
            'emptyText' => false,
            'tableOptions' => ['class' => 'table table-bordered'],
            'headerRowOptions' => ['class' => 'bg-light-gray'],
            'columns' => [
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
            [
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
            ],
        ],
    ]); ?>
</div>
<?php Pjax::end(); ?>

<script>
    $(document).ready(function () {
        window.print();
    });
</script>