<?php


/* @var $this yii\web\View */
/* @var $model common\models\Country */

$this->title = 'Add new Country';
$this->params['breadcrumbs'][] = ['label' => 'Countries', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Country';
?>
<div class="country-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
