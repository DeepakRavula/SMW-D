<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Note */

$this->title = 'Create Note';
$this->params['breadcrumbs'][] = ['label' => 'Notes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="note-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
