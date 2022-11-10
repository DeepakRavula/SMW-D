<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\LocationDebt */

$this->title = 'Create Location Debt';
$this->params['breadcrumbs'][] = ['label' => 'Location Debts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="location-debt-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
