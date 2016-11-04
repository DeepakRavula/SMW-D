<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Vacation */

$this->title = 'Create Vacation';
$this->params['breadcrumbs'][] = ['label' => 'Vacations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vacation-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
