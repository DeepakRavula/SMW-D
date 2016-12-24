<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ExamResult */

$this->title = 'Create Exam Result';
$this->params['breadcrumbs'][] = ['label' => 'Exam Results', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="exam-result-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
