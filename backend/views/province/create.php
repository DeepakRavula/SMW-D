<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Province */

$this->title = 'Create Province';
$this->params['breadcrumbs'][] = ['label' => 'Provinces', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="province-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
