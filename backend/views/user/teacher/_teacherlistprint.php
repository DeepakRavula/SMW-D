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
<?php $model = Location::findOne(['id' => Yii::$app->session->get('location_id')]); ?>
<div class="row">
    <div class="col-md-12">
        <h2 class="page-header">
            <span class="logo-lg"><b>Arcadia</b>SMW</span>
            <small class="pull-right"><?= Yii::$app->formatter->asDate('now'); ?></small>
        </h2>
    </div>
</div>
<div class="row">
    <div class="col-md-6 invoice-col">
        <div class="invoice-print-address">
            <address>
                <strong> Arcadia Music Academy ( <?= $model->name; ?> )</strong><br/>
                <?php if (!empty($model->address)): ?>
                    <?= $model->address ?><br/>
                <?php endif; ?>
                <?php if (!empty($model->city_id)): ?>
                    <?= $model->city->name ?>
                <?php endif; ?>
                <?php if (!empty($model->province_id)): ?>
                    <?= ', ' . $model->province->name ?><br/>
                <?php endif; ?>
                <?php if (!empty($model->postal_code)): ?>
                    <?= $model->postal_code ?><br/>
                <?php endif; ?>    
                <?php if (!empty($model->phone_number)): ?>
    <?= $model->phone_number ?>
<?php endif; ?>
                <br/>
                www.arcadiamusicacademy.com
            </address>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
<div>
    <h4><strong>Teacher's List for <?= $model->name; ?> Location</strong></h4></div>
<div class="user-index"> 
    <?php
    Pjax::begin([
        'id' => 'user-index',
        'timeout' => 6000
    ]);

    ?>
    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => '',
        'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'columns' => [
                [
                'attribute' => 'firstname',
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
                'label' => 'E-mail',
                'value' => function ($data) {
                    return !empty($data->email) ? $data->email : null;
                },
            ],
                [
                'label' => 'Phone',
                'value' => function ($data) {
                    return !empty($data->phoneNumber->number) ? $data->phoneNumber->number : null;
                },
            ],
        ],
    ]);

    ?>
</div>
<?php Pjax::end(); ?>
</div>

<script>
    $(document).ready(function () {
        window.print();
    });
</script>