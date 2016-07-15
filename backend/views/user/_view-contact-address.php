<?php
use yii\helpers\Html;
?>
<div class="address p-t-10 p-b-10 relative">
    <div class="col-md-2 p-0"><strong><?= Html::encode( ! empty($model->label) ? $model->label : null) ?></strong></div> 
    <div class="col-md-10">
        <div class="<?= Html::encode( ! empty($model->is_primary) ? "primary" : null); ?>">
        <?= Html::encode( ! empty($model->address) ? $model->address : null) ?><Br>
        <?= Html::encode( ! empty($model->city->name) ? $model->city->name : null) ?>, <?= Html::encode( ! empty($model->province->name) ? $model->province->name : null) ?><Br> 
        <?= Html::encode( ! empty($model->country->name) ? $model->country->name : null) ?> <?= Html::encode( ! empty($model->postal_code) ? $model->postal_code : null) ?>
    </div>
    <div class="clearfix"></div>
</div>