<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ClassRoom */

$this->title = 'Edit ' . $model->name;

?>
<div class="class-room-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
