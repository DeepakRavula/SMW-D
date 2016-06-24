<?php
use yii\helpers\Html;
?>
<div class="phone p-t-10 p-b-10">
    <div class="col-xs-2 p-0"><strong><?= Html::encode( ! empty($model->label->name) ? $model->label->name : null)  ?></strong></div>
    <div class="col-xs-3"><?= Html::encode( ! empty($model->number)) ? $model->number : null ?>
    <?= Html::encode( ! empty($model->extension) ? $model->extension : null) ?>
    </div>
    <div class="clearfix"></div>
</div>