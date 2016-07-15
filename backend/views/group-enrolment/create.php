<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\GroupEnrolment */

$this->title = 'Create Group Enrolment';
$this->params['breadcrumbs'][] = ['label' => 'Group Enrolments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="group-enrolment-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
