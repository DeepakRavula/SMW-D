<?php
use yii\helpers\Html;
?>
<div class="address">
    <?= Html::encode( ! empty($model->label) ? $model->label : null) ?> 
    <?= Html::encode( ! empty($model->address) ? $model->address : null) ?>
    <?= Html::encode( ! empty($model->city->name) ? $model->city->name : null) ?>
    <?= Html::encode( ! empty($model->province->name) ? $model->province->name : null) ?>
    <?= Html::encode( ! empty($model->postal_code) ? $model->postal_code : null) ?>
    <?= Html::encode( ! empty($model->country->name) ? $model->country->name : null) ?>
	
</div>