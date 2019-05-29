<?php

use yii\helpers\Html;
?>
    <?= Html::a('<i title="Print" class="fa fa-print"></i>', ['account-receivable-report/print', 'id' => $model->id], ['class' => 'btn btn-box-tool', 'target' => '_blank']) ?>
