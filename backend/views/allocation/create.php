<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Allocation */

$this->title = 'Create Allocation';
$this->params['breadcrumbs'][] = ['label' => 'Allocations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="allocation-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
