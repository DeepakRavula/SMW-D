<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\RemainderNotes */

$this->title = 'Create Remainder Notes';
$this->params['breadcrumbs'][] = ['label' => 'Remainder Notes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="remainder-notes-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
