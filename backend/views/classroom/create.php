<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ClassRoom */

$this->title = 'Add Classroom';
?>
<div class="class-room-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
