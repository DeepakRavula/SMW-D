<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\RemainderNotes */

$this->title = 'Update Remainder Notes';
$this->params['breadcrumbs'][] = ['label' => 'Remainder Notes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="remainder-notes-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
