<?php

use common\models\Course;

/* @var $this yii\web\View */
/* @var $model common\models\Enrolment */

$this->title = 'Create Enrolment';
$this->params['breadcrumbs'][] = ['label' => 'Enrolments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="enrolment-create">

    <?php echo $this->render('_index', [
        'model' => new Course(),
    ]) ?>

</div>
