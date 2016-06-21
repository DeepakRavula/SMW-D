<?php
use yii\helpers\Html;
?>
<div class="phone">
    <?= Html::encode( ! empty($model->label->name) ? $model->label->name : null)  ?>  
    <?= Html::encode( ! empty($model->number)) ? $model->number : null ?>
    <?= Html::encode( ! empty($model->extension) ? $model->extension : null) ?>
</div>