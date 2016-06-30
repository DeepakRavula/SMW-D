<?php
use yii\helpers\Html;
?>
<div class="phone p-t-10 p-b-10 relative">
    <div class="col-xs-2 p-0"><strong><?= Html::encode( ! empty($model->label->name) ? $model->label->name : null)  ?></strong></div>
    <div class="col-xs-10"><?= Html::encode( ! empty($model->number)) ? $model->number : null ?>
 <strong>Ext </strong>
    <?= Html::encode( ! empty($model->extension) ? $model->extension : null) ?>
    </div>
    <div class="clearfix"></div>
    <?php
            echo Html::a(Yii::t('backend', '<span class="label label-primary">Delete</span>'), ['delete-phone', 'id' => $model->id,'userId' => $model->user->id], [
                'class' => 'del-ce',
                'data' => [
                    'confirm' => Yii::t('backend', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ])
            ?>
</div>