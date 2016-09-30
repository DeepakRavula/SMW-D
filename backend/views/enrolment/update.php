<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Enrolment */

$this->title = 'Edit Enrolment';
?>
<div class="enrolment-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
