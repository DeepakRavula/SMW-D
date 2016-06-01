<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Program */

$this->title = 'Add new Program';
$this->params['breadcrumbs'][] = ['label' => 'Programs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="program-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
