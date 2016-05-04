<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Country */

$this->title = 'Create Country';
$this->params['breadcrumbs'][] = ['label' => 'Countries', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="country-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
