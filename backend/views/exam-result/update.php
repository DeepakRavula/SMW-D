<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ExamResult */

$this->title = 'Update Exam Result: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Exam Results', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="exam-result-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
